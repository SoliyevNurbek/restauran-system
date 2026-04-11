<?php

use App\Models\Page;
use App\Services\PageTranslationService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $translationService = app(PageTranslationService::class);

        $pages = DB::table('pages')
            ->where('locale', 'uz')
            ->orderBy('slug')
            ->orderBy('version')
            ->get();

        foreach ($pages as $page) {
            foreach (array_filter(Page::SUPPORTED_LOCALES, fn (string $locale) => $locale !== 'uz') as $locale) {
                $translated = $translationService->translate($page->slug, $page->title, $page->content, $locale);

                DB::table('pages')->updateOrInsert(
                    [
                        'slug' => $page->slug,
                        'locale' => $locale,
                        'version' => $page->version,
                    ],
                    [
                        'title' => $translated['title'],
                        'content' => $translated['content'],
                        'published_at' => $page->published_at,
                        'updated_by' => $page->updated_by,
                        'created_at' => $page->created_at,
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }

    public function down(): void
    {
    }
};
