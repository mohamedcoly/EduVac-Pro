<?php

namespace App\Services;

use App\Models\JobOffer;
use App\Models\TeacherProfile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TeacherMatchingService
{
    /**
     * Rank verified, available teachers for an offer.
     *
     * The offer schema currently has no required weekdays or time ranges.
     * Availability therefore measures declared availability and schedule
     * completeness, not an exact timetable overlap.
     */
    public function rank(JobOffer $offer): Collection
    {
        $teachers = TeacherProfile::query()
            ->where('verification_status', 'verified')
            ->where('is_available', true)
            ->with(['subjects', 'schoolLevels', 'availabilities'])
            ->get();

        return $teachers->map(function (TeacherProfile $teacher) use ($offer): array {
            $criteria = $this->score($teacher, $offer);

            return [
                'profile' => $teacher,
                'score' => array_sum(array_column($criteria, 'score')),
                'criteria' => $criteria,
            ];
        })->sortByDesc('score')->values();
    }

    public function score(TeacherProfile $teacher, JobOffer $offer): array
    {
        $subjectMatch = $teacher->subjects->contains('id', $offer->subject_id);
        $levelMatch = $teacher->schoolLevels->contains('id', $offer->school_level_id);

        $offerCity = Str::lower(trim((string) $offer->city));
        $teacherCity = Str::lower(trim((string) $teacher->city));
        $areas = collect($teacher->mobility_areas ?? [])
            ->map(fn ($area) => Str::lower(trim((string) $area)));

        $geoScore = 0;
        $geoLabel = 'Zone non correspondante';
        if ($offerCity !== '' && $teacherCity === $offerCity) {
            $geoScore = 20;
            $geoLabel = 'Même ville';
        } elseif ($offerCity !== '' && $areas->contains($offerCity)) {
            $geoScore = 16;
            $geoLabel = 'Zone de mobilité déclarée';
        } elseif ($teacherCity !== '') {
            $geoScore = 5;
            $geoLabel = 'Ville différente';
        }

        $availabilityScore = 0;
        $availabilityLabel = 'Non disponible';
        if ($teacher->is_available && $teacher->availabilities->isNotEmpty()) {
            $availabilityScore = 15;
            $availabilityLabel = 'Disponible, planning renseigné';
        } elseif ($teacher->is_available) {
            $availabilityScore = 8;
            $availabilityLabel = 'Disponible, planning à préciser';
        }

        $experience = (int) $teacher->years_experience;
        $experienceScore = match (true) {
            $experience >= 5 => 10,
            $experience >= 3 => 8,
            $experience >= 1 => 5,
            default => 2,
        };

        return [
            'subject' => ['label' => $subjectMatch ? 'Matière correspondante' : 'Matière différente', 'score' => $subjectMatch ? 30 : 0, 'max' => 30],
            'level' => ['label' => $levelMatch ? 'Niveau correspondant' : 'Niveau différent', 'score' => $levelMatch ? 25 : 0, 'max' => 25],
            'location' => ['label' => $geoLabel, 'score' => $geoScore, 'max' => 20],
            'availability' => ['label' => $availabilityLabel, 'score' => $availabilityScore, 'max' => 15],
            'experience' => ['label' => $experience.' an(s) d’expérience', 'score' => $experienceScore, 'max' => 10],
        ];
    }
}
