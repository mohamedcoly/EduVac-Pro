<?php

namespace App\Http\Controllers;

use App\Models\TeacherDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TeacherDocumentController extends Controller
{
    public function download(Request $request, TeacherDocument $document): StreamedResponse
    {
        $this->authorize('view', $document);

        abort_unless(Storage::disk($document->disk)->exists($document->path), 404);

        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($document->original_name));

        return Storage::disk($document->disk)->download(
            $document->path,
            $safeName ?: 'document'
        );
    }
}
