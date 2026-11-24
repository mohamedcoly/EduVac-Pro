<?php

namespace App\Http\Controllers;

use App\Models\SchoolLevel;
use App\Models\Subject;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherDirectoryController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasAnyRole(['school_manager', 'school_recruiter', 'platform_admin']), 403);

        $query = TeacherProfile::visibleTo($request->user())
            ->with(['subjects', 'schoolLevels'])
            ->where('is_available', true);

        if ($request->filled('q')) {
            $term = trim($request->string('q')->toString());
            $query->where(function ($builder) use ($term): void {
                $builder->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('city', 'like', "%{$term}%");
            });
        }
        if ($request->filled('subject_id')) {
            $query->whereHas('subjects', fn ($q) => $q->whereKey($request->integer('subject_id')));
        }
        if ($request->filled('school_level_id')) {
            $query->whereHas('schoolLevels', fn ($q) => $q->whereKey($request->integer('school_level_id')));
        }
        if ($request->filled('city')) {
            $query->where('city', 'like', '%'.trim($request->string('city')->toString()).'%');
        }
        if ($request->filled('min_experience')) {
            $query->where('years_experience', '>=', $request->integer('min_experience'));
        }

        return view('schools.teachers.index', [
            'teachers' => $query->orderByDesc('profile_completion')->paginate(12)->withQueryString(),
            'subjects' => Subject::orderBy('name')->get(),
            'schoolLevels' => SchoolLevel::orderBy('name')->get(),
        ]);
    }
}
