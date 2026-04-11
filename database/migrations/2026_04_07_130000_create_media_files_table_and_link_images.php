<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('media_files')) {
            Schema::create('media_files', function (Blueprint $table) {
                $table->id();
                $table->string('filename')->nullable();
                $table->string('mime_type', 150)->nullable();
                $table->unsignedBigInteger('size')->default(0);
                $table->longText('content_base64');
                $table->timestamps();
            });
        }

        $this->addColumnIfMissing('media_assets', 'media_file_id');
        $this->addColumnIfMissing('settings', 'logo_media_file_id');
        $this->addColumnIfMissing('settings', 'favicon_media_file_id');
        $this->addColumnIfMissing('halls', 'image_media_file_id');
        $this->addColumnIfMissing('wedding_packages', 'image_media_file_id');
        $this->addColumnIfMissing('wedding_package_images', 'media_file_id');
        $this->addColumnIfMissing('bookings', 'package_image_media_file_id');

        $this->backfillMediaAssets();
        $this->backfillSettings();
        $this->backfillHalls();
        $this->backfillWeddingPackages();
        $this->backfillWeddingPackageImages();
        $this->backfillBookings();
    }

    public function down(): void
    {
        $this->dropColumnIfExists('bookings', 'package_image_media_file_id');
        $this->dropColumnIfExists('wedding_package_images', 'media_file_id');
        $this->dropColumnIfExists('wedding_packages', 'image_media_file_id');
        $this->dropColumnIfExists('halls', 'image_media_file_id');
        $this->dropColumnIfExists('settings', 'favicon_media_file_id');
        $this->dropColumnIfExists('settings', 'logo_media_file_id');
        $this->dropColumnIfExists('media_assets', 'media_file_id');

        Schema::dropIfExists('media_files');
    }

    private function addColumnIfMissing(string $table, string $column): void
    {
        if (! Schema::hasTable($table) || Schema::hasColumn($table, $column)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            $blueprint->unsignedBigInteger($column)->nullable();
        });
    }

    private function dropColumnIfExists(string $table, string $column): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            $blueprint->dropColumn($column);
        });
    }

    private function backfillMediaAssets(): void
    {
        if (! Schema::hasTable('media_assets')) {
            return;
        }

        DB::table('media_assets')
            ->whereNull('media_file_id')
            ->whereNotNull('path')
            ->orderBy('id')
            ->each(function (object $asset): void {
                $mediaFileId = $this->createMediaFileFromPublicPath($asset->path);

                if ($mediaFileId) {
                    DB::table('media_assets')->where('id', $asset->id)->update(['media_file_id' => $mediaFileId]);
                }
            });
    }

    private function backfillSettings(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        DB::table('settings')->orderBy('id')->each(function (object $setting): void {
            $updates = [];

            if (Schema::hasColumn('settings', 'logo_path') && ! $setting->logo_media_file_id && filled($setting->logo_path)) {
                $updates['logo_media_file_id'] = $this->createMediaFileFromPublicPath($setting->logo_path);
            }

            if (Schema::hasColumn('settings', 'favicon_path') && ! $setting->favicon_media_file_id && filled($setting->favicon_path)) {
                $updates['favicon_media_file_id'] = $this->createMediaFileFromPublicPath($setting->favicon_path);
            }

            if ($updates !== []) {
                DB::table('settings')->where('id', $setting->id)->update($updates);
            }
        });
    }

    private function backfillHalls(): void
    {
        if (! Schema::hasTable('halls')) {
            return;
        }

        DB::table('halls')
            ->whereNull('image_media_file_id')
            ->whereNotNull('image')
            ->orderBy('id')
            ->each(function (object $hall): void {
                $mediaFileId = $this->createMediaFileFromPublicPath($hall->image);

                if ($mediaFileId) {
                    DB::table('halls')->where('id', $hall->id)->update(['image_media_file_id' => $mediaFileId]);
                }
            });
    }

    private function backfillWeddingPackages(): void
    {
        if (! Schema::hasTable('wedding_packages')) {
            return;
        }

        DB::table('wedding_packages')
            ->whereNull('image_media_file_id')
            ->whereNotNull('image')
            ->orderBy('id')
            ->each(function (object $package): void {
                $mediaFileId = $this->createMediaFileFromPublicPath($package->image);

                if ($mediaFileId) {
                    DB::table('wedding_packages')->where('id', $package->id)->update(['image_media_file_id' => $mediaFileId]);
                }
            });
    }

    private function backfillWeddingPackageImages(): void
    {
        if (! Schema::hasTable('wedding_package_images')) {
            return;
        }

        DB::table('wedding_package_images')
            ->whereNull('media_file_id')
            ->whereNotNull('image_path')
            ->orderBy('id')
            ->each(function (object $image): void {
                $mediaFileId = $this->createMediaFileFromPublicPath($image->image_path);

                if ($mediaFileId) {
                    DB::table('wedding_package_images')->where('id', $image->id)->update(['media_file_id' => $mediaFileId]);
                }
            });
    }

    private function backfillBookings(): void
    {
        if (! Schema::hasTable('bookings')) {
            return;
        }

        DB::table('bookings')
            ->whereNull('package_image_media_file_id')
            ->whereNotNull('package_image_path')
            ->orderBy('id')
            ->each(function (object $booking): void {
                $mediaFileId = $this->createMediaFileFromPublicPath($booking->package_image_path);

                if ($mediaFileId) {
                    DB::table('bookings')->where('id', $booking->id)->update(['package_image_media_file_id' => $mediaFileId]);
                }
            });
    }

    private function createMediaFileFromPublicPath(?string $path): ?int
    {
        $path = trim((string) $path);

        if ($path === '' || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $contents = Storage::disk('public')->get($path);
        $absolutePath = Storage::disk('public')->path($path);
        $mimeType = @mime_content_type($absolutePath) ?: Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';
        $size = (int) (Storage::disk('public')->size($path) ?: strlen($contents));

        return (int) DB::table('media_files')->insertGetId([
            'filename' => basename($path),
            'mime_type' => $mimeType,
            'size' => $size,
            'content_base64' => base64_encode($contents),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
