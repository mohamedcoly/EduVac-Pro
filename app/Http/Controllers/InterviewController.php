<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Interview;
use App\Notifications\EduVacActivityNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InterviewController extends Controller
{
    public function store(Request $request, Application $application): RedirectResponse
    {
        $this->authorize('manage', $application);

        abort_unless(in_array($application->status, ['shortlisted', 'interview'], true), 422,
            'Seules les candidatures présélectionnées peuvent recevoir un entretien.');

        $data = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
            'format' => ['required', Rule::in(['in_person', 'online', 'phone'])],
            'location_or_link' => ['nullable', 'string', 'max:500'],
            'internal_notes' => ['nullable', 'string', 'max:3000'],
        ]);

        DB::transaction(function () use ($request, $application, $data): void {
            $application->interviews()->create([
                ...$data,
                'scheduled_by' => $request->user()->id,
                'status' => 'scheduled',
                'teacher_response' => 'pending',
            ]);
            $application->update(['status' => 'interview']);
        });

        $application->load('teacherProfile.user', 'jobOffer');
        $application->teacherProfile?->user?->notify(new EduVacActivityNotification(
            'Entretien planifié',
            'Un entretien est prévu pour votre candidature « '.$application->jobOffer->title.' ».',
            route('applications.index'),
            'interview_scheduled',
        ));

        return back()->with('status', 'Entretien planifié. L’enseignant peut maintenant confirmer ou répondre.');
    }

    public function respond(Request $request, Interview $interview): RedirectResponse
    {
        $this->authorize('respond', $interview);

        abort_unless($interview->status === 'scheduled' && $interview->teacher_response === 'pending', 422,
            'Cet entretien a déjà reçu une réponse ou n’est plus actif.');

        $data = $request->validate([
            'teacher_response' => ['required', Rule::in(['accepted', 'declined', 'reschedule_requested'])],
            'teacher_response_note' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($data['teacher_response'] === 'reschedule_requested'
            && blank($data['teacher_response_note'] ?? null)) {
            return back()->withErrors([
                'teacher_response_note' => 'Précisez brièvement vos disponibilités ou le motif de report.',
            ])->withInput();
        }

        $interview->update([
            ...$data,
            'responded_at' => now(),
        ]);

        if ($data['teacher_response'] === 'declined') {
            $interview->update(['status' => 'cancelled']);
        }

        $interview->load('scheduler');
        $interview->scheduler?->notify(new EduVacActivityNotification(
            'Réponse à un entretien',
            'L’enseignant a répondu à votre invitation d’entretien.',
            route('school-applications.index'),
            'interview_teacher_response',
        ));

        return back()->with('status', 'Votre réponse à l’entretien a été enregistrée.');
    }

    public function cancel(Request $request, Interview $interview): RedirectResponse
    {
        $this->authorize('schedule', $interview);

        abort_unless($interview->status === 'scheduled', 422, 'Cet entretien n’est plus actif.');

        $interview->update(['status' => 'cancelled']);
        $interview->load('application.teacherProfile.user');
        $interview->application?->teacherProfile?->user?->notify(new EduVacActivityNotification(
            'Entretien annulé',
            'L’établissement a annulé votre entretien.',
            route('applications.index'),
            'interview_cancelled',
        ));

        return back()->with('status', 'Entretien annulé.');
    }
}