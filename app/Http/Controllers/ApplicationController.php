<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\JobOffer;
use App\Notifications\EduVacActivityNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $applications = Application::visibleTo($request->user())
            ->with(['jobOffer.school', 'jobOffer.subject', 'teacherProfile.subjects', 'teacherProfile.user', 'teacherDocument', 'interviews', 'mission'])
            ->latest('submitted_at')
            ->paginate(15);

        return view('applications.index', compact('applications'));
    }

    public function store(Request $request, JobOffer $offer): RedirectResponse
    {
        $this->authorize('apply', $offer);
        $profile = $request->user()->teacherProfile;

        if ($offer->applications()->where('teacher_profile_id', $profile->id)->exists()) {
            return back()->withErrors(['application' => 'Vous avez déjà candidaté à cette offre.']);
        }

        $data = $request->validate([
            'cover_message' => ['nullable', 'string', 'max:3000'],
            'teacher_document_id' => ['required', 'integer', 'exists:teacher_documents,id'],
        ]);

        $ownsCv = $profile->documents()
            ->whereKey($data['teacher_document_id'])
            ->where('type', 'cv')
            ->exists();

        if (! $ownsCv) {
            return back()->withErrors(['teacher_document_id' => 'Sélectionnez un CV de votre propre profil.'])->withInput();
        }

        $application = Application::create([
            'job_offer_id' => $offer->id,
            'teacher_profile_id' => $profile->id,
            'cover_message' => $data['cover_message'] ?? null,
            'teacher_document_id' => $data['teacher_document_id'],
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $offer->school->members()->with('user')->get()->each(function ($member) use ($offer): void {
            $member->user?->notify(new EduVacActivityNotification(
                'Nouvelle candidature',
                'Un enseignant vient de candidater à « '.$offer->title.' ».',
                route('school-applications.index'),
                'application_submitted',
            ));
        });

        return back()->with('status', 'Votre candidature a été transmise.');
    }

    public function updateStatus(Request $request, Application $application): RedirectResponse
    {
        $this->authorize('manage', $application);
        $data = $request->validate([
            'status' => ['required', 'in:shortlisted,interview,selected,rejected'],
        ]);
        $application->update(['status' => $data['status']]);
        $application->load('teacherProfile.user', 'jobOffer');
        $application->teacherProfile?->user?->notify(new EduVacActivityNotification(
            'Votre candidature a évolué',
            'Le statut de votre candidature pour « '.$application->jobOffer->title.' » a été mis à jour.',
            route('applications.index'),
            'application_status_changed',
        ));

        return back()->with('status', 'Statut de candidature mis à jour.');
    }

    public function withdraw(Request $request, Application $application): RedirectResponse
    {
        $this->authorize('withdraw', $application);
        $application->update(['status' => 'withdrawn']);
        return back()->with('status', 'Votre candidature a été retirée.');
    }
}
