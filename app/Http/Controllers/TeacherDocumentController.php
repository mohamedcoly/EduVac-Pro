<?php

namespace App\Http\Controllers;

use App\Models\TeacherDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherDocumentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $profile = $request->user()->teacherProfile()->firstOrFail();

        $data = $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'type' => ['required', 'in:cv,diploma,certificate,identity,other'],
        ]);

        $file = $data['document'];
        $path = $file->store('teacher-documents/'.$profile->id, 'private');

        $profile->documents()->create([
            'type' => $data['type'],
            'disk' => 'private',
            'path' => $path,
            'original_name' => Str::limit(basename($file->getClientOriginalName()), 180, ''),
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'size_bytes' => $file->getSize(),
            'verification_status' => 'pending',
        ]);

        $checks = [
            filled($profile->first_name) && filled($profile->last_name),
            filled($profile->phone),
            filled($profile->biography),
            $profile->subjects()->exists(),
            $profile->schoolLevels()->exists(),
            $profile->documents()->where('type', 'cv')->exists(),
            $profile->availabilities()->exists(),
        ];
        $profile->profile_completion = (int) round(count(array_filter($checks)) / count($checks) * 100);

        if ($profile->profile_completion === 100 && in_array($profile->verification_status, ['incomplete', 'rejected'], true)) {
            $profile->verification_status = 'pending_review';
        }
        $profile->save();

        return back()->with('status', 'Document ajouté. Il reste privé et pourra être vérifié par l’équipe EduVac Pro.');
    }

    public function download(TeacherDocument $document)
    {
        $this->authorize('view', $document);

        abort_unless($document->disk === 'private', 404);
        abort_unless(Storage::disk('private')->exists($document->path), 404);

        $filename = preg_replace('/[^A-Za-z0-9._-]/', '_', $document->original_name);

        return Storage::disk('private')->download($document->path, $filename);
    }
}
