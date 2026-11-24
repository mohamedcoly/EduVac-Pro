<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherJobOfferController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasRole('teacher'), 403);
        $offers = JobOffer::visibleTo($request->user())
            ->with(['school', 'subject', 'schoolLevel'])
            ->where('status', 'published')
            ->whereHas('school', fn ($q) => $q->where('verification_status', 'verified'))
            ->when($request->filled('subject_id'), fn ($q) => $q->where('subject_id', $request->integer('subject_id')))
            ->when($request->filled('school_level_id'), fn ($q) => $q->where('school_level_id', $request->integer('school_level_id')))
            ->when($request->filled('city'), fn ($q) => $q->where('city', 'like', '%'.trim($request->string('city')->toString()).'%'))
            ->where(fn ($q) => $q->whereNull('application_deadline')->orWhereDate('application_deadline', '>=', today()))
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        return view('teachers.offers.index', compact('offers'));
    }

    public function show(Request $request, JobOffer $offer): View
    {
        $this->authorize('view', $offer);
        abort_unless($offer->status === 'published', 404);
        $profile = $request->user()->teacherProfile;
        $hasApplied = $profile && $offer->applications()->where('teacher_profile_id', $profile->id)->exists();

        return view('teachers.offers.show', [
            'offer' => $offer->load(['school', 'subject', 'schoolLevel']),
            'profile' => $profile?->load('documents'),
            'hasApplied' => $hasApplied,
        ]);
    }
}
