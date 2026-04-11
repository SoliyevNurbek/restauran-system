<?php

namespace App\Http\Controllers;

use App\Models\MediaFile;
use Illuminate\Http\Response;

class MediaFileController extends Controller
{
    public function __invoke(MediaFile $mediaFile): Response
    {
        return response($mediaFile->content(), 200, [
            'Content-Type' => $mediaFile->mime_type ?: 'application/octet-stream',
            'Content-Length' => (string) $mediaFile->size,
            'Cache-Control' => 'public, max-age=31536000',
            'Content-Disposition' => 'inline; filename="'.$this->safeFilename($mediaFile->filename).'"',
        ]);
    }

    private function safeFilename(?string $filename): string
    {
        $filename = trim((string) $filename);

        return $filename !== '' ? str_replace('"', '', $filename) : 'media-file';
    }
}
