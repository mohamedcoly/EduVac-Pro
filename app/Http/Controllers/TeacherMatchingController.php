<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use App\Services\TeacherMatchingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherMatchingController extends Controller
{
    public function index(Request $request, JobOffer $offer, TeacherMatchingService $matching): View
    {
        $this->authorize('view', $offer);

        abort_unless($request->user()->hasAnyRole(['platform_admin', 'school_manager', 'school_recruiter']), 403);

        return view('schools.offers.matching', [
            'offer' => $offer->load(['school', 'subject', 'schoolLevel']),
            'matches' => $matching->rank($offer),
        ]);
    }
}
