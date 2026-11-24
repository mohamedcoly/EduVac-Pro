<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\TeacherProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VerificationController extends Controller
{
    public function updateSchool(Request $request, School $school): RedirectResponse
    {
        $this->authorize('verify', $school);

        $data = $request->validate([
            'verification_status' => ['required', Rule::in(['verified', 'rejected', 'suspended'])],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $school, $data): void {
            $school->forceFill([
                'verification_status' => $data['verification_status'],
                'verified_by' => $data['verification_status'] === 'verified' ? $request->user()->id : null,
                'verified_at' => $data['verification_status'] === 'verified' ? now() : null,
            ])->save();

            DB::table('audit_logs')->insert([
                'actor_id' => $request->user()->id,
                'action' => 'school.verification_'.$data['verification_status'],
                'subject_type' => School::class,
                'subject_id' => $school->id,
                'metadata' => json_encode(['reason' => $data['reason'] ?? null], JSON_THROW_ON_ERROR),
                'ip_address' => $request->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return back()->with('status', 'Le statut de vérification de l’établissement a été mis à jour.');
    }

    public function updateTeacher(Request $request, TeacherProfile $profile): RedirectResponse
    {
        $this->authorize('verify', $profile);

        $data = $request->validate([
            'verification_status' => ['required', Rule::in(['verified', 'rejected', 'suspended'])],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $profile, $data): void {
            $profile->update(['verification_status' => $data['verification_status']]);

            DB::table('audit_logs')->insert([
                'actor_id' => $request->user()->id,
                'action' => 'teacher.verification_'.$data['verification_status'],
                'subject_type' => TeacherProfile::class,
                'subject_id' => $profile->id,
                'metadata' => json_encode(['reason' => $data['reason'] ?? null], JSON_THROW_ON_ERROR),
                'ip_address' => $request->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return back()->with('status', 'Le statut de vérification de l’enseignant a été mis à jour.');
    }
}
