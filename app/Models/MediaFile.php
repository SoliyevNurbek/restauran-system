<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class MediaFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'mime_type',
        'size',
        'content_base64',
    ];

    public function url(): string
    {
        return route('media.show', [
            'mediaFile' => $this->getKey(),
            'filename' => $this->filename ?: 'file',
        ]);
    }

    public function content(): string
    {
        return base64_decode((string) $this->content_base64, true) ?: '';
    }

    public static function createFromUpload(UploadedFile $file): self
    {
        return static::create([
            'filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'size' => (int) $file->getSize(),
            'content_base64' => base64_encode($file->get()),
        ]);
    }

    public static function cloneFrom(?self $source, ?string $filename = null): ?self
    {
        if (! $source) {
            return null;
        }

        return static::create([
            'filename' => $filename ?: $source->filename,
            'mime_type' => $source->mime_type,
            'size' => $source->size,
            'content_base64' => $source->content_base64,
        ]);
    }
}
