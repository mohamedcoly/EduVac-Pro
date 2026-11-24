<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->hasRole('platform_admin')) {
            return view('dashboards.admin', compact('user'));
        }

        if ($user->hasAnyRole(['school_manager', 'school_recruiter'])) {
            $school = $user->schoolMemberships()->with('school')->first()?->school;

            return view('dashboards.school', compact('user', 'school'));
        }

        if ($user->hasRole('teacher')) {
            $profile = $user->teacherProfile;

            return view('dashboards.teacher', compact('user', 'profile'));
        }

        abort(403, 'Aucun espace n’est associé à ce compte.');
    }
}
