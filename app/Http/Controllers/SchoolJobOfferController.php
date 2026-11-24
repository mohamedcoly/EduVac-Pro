<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use App\Models\School;
use App\Models\SchoolLevel;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolJobOfferController extends Controller
{
    public function index(Request $request): View
    {
        $offers = JobOffer::visibleTo($request->user())
            ->with(['subject', 'schoolLevel'])
            ->withCount('applications')
            ->latest()
            ->paginate(12);

        return view('schools.offers.index', compact('offers'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', JobOffer::class);

        return view('schools.offers.form', [
            'offer' => new JobOffer(),
            'schools' => School::visibleTo($request->user())->where('verification_status', 'verified')->get(),
            'subjects' => Subject::orderBy('name')->get(),
            'schoolLevels' => SchoolLevel::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', JobOffer::class);
        $data = $this->validated($request);
        $school = School::visibleTo($request->user())->where('verification_status', 'verified')->findOrFail($data['school_id']);
        $offer = $school->jobOffers()->create([
            ...$data,
            'created_by' => $request->user()->id,
            'status' => 'draft',
        ]);

        return redirect()->route('school-offers.edit', $offer)->with('status', 'Offre enregistrée en brouillon.');
    }

    public function edit(Request $request, JobOffer $offer): View
    {
        $this->authorize('update', $offer);
        return view('schools.offers.form', [
            'offer' => $offer,
            'schools' => School::visibleTo($request->user())->get(),
            'subjects' => Subject::orderBy('name')->get(),
            'schoolLevels' => SchoolLevel::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, JobOffer $offer): RedirectResponse
    {
        $this->authorize('update', $offer);
        $data = $this->validated($request);
        abort_unless((int) $data['school_id'] === $offer->school_id, 422, 'Le rattachement de l’offre ne peut pas être modifié.');
        $offer->update(collect($data)->except('school_id')->all());

        return back()->with('status', 'Offre mise à jour.');
    }

    public function publish(Request $request, JobOffer $offer): RedirectResponse
    {
        $this->authorize('publish', $offer);
        $offer->update(['status' => 'published', 'published_at' => now()]);
        return back()->with('status', 'Offre publiée. Elle est maintenant visible par les enseignants vérifiés.');
    }

    public function close(Request $request, JobOffer $offer): RedirectResponse
    {
        $this->authorize('update', $offer);
        $offer->update(['status' => 'closed']);
        return back()->with('status', 'Offre clôturée.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'school_id' => ['required', 'integer', 'exists:schools,id'],
            'title' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'min:30', 'max:12000'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'school_level_id' => ['required', 'integer', 'exists:school_levels,id'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'weekly_hours' => ['nullable', 'integer', 'min:1', 'max:40'],
            'salary_min_fcfa' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'salary_max_fcfa' => ['nullable', 'integer', 'min:0', 'max:100000000', 'gte:salary_min_fcfa'],
            'starts_on' => ['nullable', 'date', 'after_or_equal:today'],
            'application_deadline' => ['nullable', 'date', 'after_or_equal:today'],
        ]);
    }
}
