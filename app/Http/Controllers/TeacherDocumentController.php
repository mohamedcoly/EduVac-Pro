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
