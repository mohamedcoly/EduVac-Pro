<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\JobOffer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $applications = Application::visibleTo($request->user())
            ->with(['jobOffer.school', 'jobOffer.subject', 'teacherProfile.subjects', 'teacherProfile.user'])
            ->latest('submitted_at')
            ->paginate(15);

        return view('applications.index', compact('applications'));
    }

    public function store(Request $request, JobOffer $offer): RedirectResponse
    {
        $this->authorize('apply', $offer);
        $data = $request->validate([
            'cover_message' => ['nullable', 'string', 'max:3000'],
            'teacher_document_id' => ['nullable', 'integer', 'exists:teacher_documents,id'],
        ]);

        $profile = $request->user()->teacherProfile;
        if (!empty($data['teacher_document_id'])) {
            abort_unless($profile->documents()->whereKey($data['teacher_document_id'])->where('type', 'cv')->exists(), 422, 'Sélectionnez votre propre CV.');
        } else {
            $data['teacher_document_id'] = $profile->documents()->where('type', 'cv')->latest()->value('id');
        }
        if (!$data['teacher_document_id']) {
            return back()->withErrors(['teacher_document_id' => 'Ajoutez un CV avant de candidater.'])->withInput();
        }

        Application::create([
            'job_offer_id' => $offer->id,
            'teacher_profile_id' => $profile->id,
            'cover_message' => $data['cover_message'] ?? null,
            'teacher_document_id' => $data['teacher_document_id'],
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return back()->with('status', 'Votre candidature a été transmise.');
    }

    public function updateStatus(Request $request, Application $application): RedirectResponse
    {
        $this->authorize('manage', $application);
        $data = $request->validate([
            'status' => ['required', 'in:shortlisted,interview,selected,rejected'],
        ]);
        $application->update(['status' => $data['status']]);

        return back()->with('status', 'Statut de candidature mis à jour.');
    }

    public function withdraw(Request $request, Application $application): RedirectResponse
    {
        $this->authorize('withdraw', $application);
        $application->update(['status' => 'withdrawn']);
        return back()->with('status', 'Votre candidature a été retirée.');
    }
}
