<?php

use App\Models\Page;
use App\Services\PageTranslationService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('locale', 5)->default('uz')->after('slug');
        });

        DB::table('pages')->update(['locale' => 'uz']);

        Schema::table('pages', function (Blueprint $table) {
            $table->dropUnique('pages_slug_version_unique');
            $table->unique(['slug', 'locale', 'version']);
            $table->index(['slug', 'locale', 'published_at']);
        });

        $translationService = app(PageTranslationService::class);
        $pages = DB::table('pages')
            ->where('locale', 'uz')
            ->orderBy('id')
            ->get();

        foreach ($pages as $page) {
            foreach (array_filter(Page::SUPPORTED_LOCALES, fn (string $locale) => $locale !== 'uz') as $locale) {
                $translated = $translationService->translate($page->slug, $page->title, $page->content, $locale);

                DB::table('pages')->insert([
                    'slug' => $page->slug,
                    'locale' => $locale,
                    'title' => $translated['title'],
                    'content' => $translated['content'],
                    'version' => $page->version,
                    'published_at' => $page->published_at,
                    'updated_by' => $page->updated_by,
                    'created_at' => $page->created_at,
                    'updated_at' => $page->updated_at,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('pages')->where('locale', '!=', 'uz')->delete();

        Schema::table('pages', function (Blueprint $table) {
            $table->dropUnique('pages_slug_locale_version_unique');
            $table->dropIndex('pages_slug_locale_published_at_index');
            $table->unique(['slug', 'version']);
            $table->dropColumn('locale');
        });
    }
};
