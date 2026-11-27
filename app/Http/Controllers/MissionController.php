<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Mission;
use App\Notifications\EduVacActivityNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MissionController extends Controller
{
    public function store(Request $request, Application $application): RedirectResponse
    {
        $this->authorize('manage', $application);

        abort_unless($application->status === 'selected', 422,
            'La mission ne peut être proposée qu’après la sélection du candidat.');
        abort_if($application->mission()->exists(), 409, 'Une mission existe déjà pour cette candidature.');

        $data = $request->validate([
            'starts_on' => ['required', 'date', 'after_or_equal:today'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'weekly_hours' => ['nullable', 'integer', 'min:1', 'max:60'],
            'pay_amount_fcfa' => ['required', 'integer', 'min:1', 'max:100000000'],
            'payment_frequency' => ['required', Rule::in(['hourly', 'weekly', 'monthly', 'fixed'])],
            'schedule_details' => ['nullable', 'string', 'max:3000'],
            'workplace_address' => ['nullable', 'string', 'max:500'],
            'mission_description' => ['required', 'string', 'min:20', 'max:5000'],
        ]);

        $mission = DB::transaction(function () use ($request, $application, $data) {
            return $application->mission()->create([
                ...$data,
                'proposed_by' => $request->user()->id,
                'status' => 'planned',
                'teacher_confirmation' => 'pending',
            ]);
        });

        $application->load('teacherProfile.user', 'jobOffer');
        $application->teacherProfile?->user?->notify(new EduVacActivityNotification(
            'Proposition de mission',
            'Une mission vous a été proposée pour votre candidature « '.$application->jobOffer->title.' ».',
            route('applications.index'),
            'mission_proposed',
        ));

        return back()->with('status', 'Proposition de mission envoyée à l’enseignant.');
    }

    public function respond(Request $request, Mission $mission): RedirectResponse
    {
        $this->authorize('respond', $mission);

        abort_unless($mission->status === 'planned' && $mission->teacher_confirmation === 'pending', 422,
            'Cette proposition n’est plus en attente de réponse.');

        $data = $request->validate([
            'teacher_confirmation' => ['required', Rule::in(['accepted', 'declined'])],
            'teacher_response_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $mission->update([
            'teacher_confirmation' => $data['teacher_confirmation'],
            'teacher_response_note' => $data['teacher_response_note'] ?? null,
            'confirmed_at' => now(),
            'status' => $data['teacher_confirmation'] === 'accepted' ? 'active' : 'cancelled',
        ]);

        $mission->load('proposer');
        $mission->proposer?->notify(new EduVacActivityNotification(
            'Réponse à la proposition de mission',
            $data['teacher_confirmation'] === 'accepted'
                ? 'L’enseignant a accepté la mission proposée.'
                : 'L’enseignant a décliné la mission proposée.',
            route('school-applications.index'),
            'mission_teacher_response',
        ));

        return back()->with('status', $data['teacher_confirmation'] === 'accepted'
            ? 'Mission acceptée. Elle est maintenant active.'
            : 'Proposition de mission déclinée.');
    }
}