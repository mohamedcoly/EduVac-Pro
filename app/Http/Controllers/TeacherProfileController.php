<?php

namespace App\Http\Controllers;

use App\Models\SchoolLevel;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeacherProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $profile = $request->user()->teacherProfile()
            ->with(['subjects', 'schoolLevels', 'availabilities', 'documents'])
            ->firstOrFail();

        return view('teachers.profile', [
            'profile' => $profile,
            'subjects' => Subject::orderBy('name')->get(),
            'schoolLevels' => SchoolLevel::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $profile = $request->user()->teacherProfile()->firstOrFail();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['required', 'string', 'max:100'],
            'biography' => ['nullable', 'string', 'max:2000'],
            'years_experience' => ['required', 'integer', 'min:0', 'max:60'],
            'mobility_radius_km' => ['nullable', 'string', 'max:50'],
            'is_available' => ['required', 'boolean'],
            'mobility_areas' => ['nullable', 'array', 'max:20'],
            'mobility_areas.*' => ['string', 'max:100'],
            'subject_ids' => ['nullable', 'array', 'max:30'],
            'subject_ids.*' => ['integer', Rule::exists('subjects', 'id')],
            'school_level_ids' => ['nullable', 'array', 'max:10'],
            'school_level_ids.*' => ['integer', Rule::exists('school_levels', 'id')],
            'availabilities' => ['nullable', 'array', 'max:21'],
            'availabilities.*.weekday' => ['required', 'integer', 'between:1,7'],
            'availabilities.*.starts_at' => ['required', 'date_format:H:i'],
            'availabilities.*.ends_at' => ['required', 'date_format:H:i', 'after:availabilities.*.starts_at'],
        ]);

        DB::transaction(function () use ($profile, $data): void {
            $profile->fill([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'] ?? null,
                'city' => $data['city'],
                'biography' => $data['biography'] ?? null,
                'years_experience' => $data['years_experience'],
                'mobility_radius_km' => $data['mobility_radius_km'] ?? null,
                'mobility_areas' => array_values(array_unique($data['mobility_areas'] ?? [])),
                'is_available' => (bool) $data['is_available'],
            ]);

            $profile->subjects()->sync($data['subject_ids'] ?? []);
            $profile->schoolLevels()->sync($data['school_level_ids'] ?? []);

            $profile->availabilities()->delete();
            foreach ($data['availabilities'] ?? [] as $slot) {
                if ($slot['ends_at'] <= $slot['starts_at']) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'availabilities' => 'L’heure de fin doit être postérieure à l’heure de début.',
                    ]);
                }
                $profile->availabilities()->create([
                    'weekday' => $slot['weekday'],
                    'starts_at' => $slot['starts_at'],
                    'ends_at' => $slot['ends_at'],
                ]);
            }

            $required = [
                filled($profile->first_name) && filled($profile->last_name),
                filled($profile->phone),
                filled($profile->biography),
                $profile->years_experience >= 0,
                $profile->subjects()->exists(),
                $profile->schoolLevels()->exists(),
                $profile->documents()->where('type', 'cv')->exists(),
                $profile->availabilities()->exists(),
            ];
            $completion = (int) round(count(array_filter($required)) / count($required) * 100);
            $profile->profile_completion = $completion;

            if ($completion === 100 && in_array($profile->verification_status, ['incomplete', 'rejected'], true)) {
                $profile->verification_status = 'pending_review';
            }

            $profile->save();
        });

        return back()->with('status', 'Votre profil enseignant a été mis à jour.');
    }
}
