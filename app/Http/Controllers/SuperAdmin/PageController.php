<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\SuperAdmin\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class PageController extends Controller
{
    private const PAGE_META = [
        Page::TERMS_OF_USE => [
            'label' => 'Foydalanish shartlari',
            'description' => 'Register sahifasidagi foydalanish shartlari hujjati.',
        ],
        Page::PRIVACY_POLICY => [
            'label' => 'Maxfiylik siyosati',
            'description' => 'Register sahifasidagi maxfiylik siyosati hujjati.',
        ],
    ];

    public function edit(Request $request): View
    {
        $slug = $this->resolveSlug($request->query('slug'));

        return view('superadmin.pages.edit', [
            'pageTitle' => 'Huquqiy sahifalar',
            'slug' => $slug,
            'pages' => self::PAGE_META,
            'currentPage' => Page::currentPublished($slug, 'uz'),
            'history' => Page::historyForSlug($slug, 'uz', 12),
        ]);
    }

    public function update(Request $request, AuditLogService $audit): RedirectResponse
    {
        $data = $request->validate([
            'slug' => ['required', 'in:'.implode(',', Page::allowedSlugs())],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:50000'],
            'published_at' => ['nullable', 'date'],
        ]);

        $current = Page::currentPublished($data['slug'], 'uz');
        $publishedAt = filled($data['published_at']) ? Carbon::parse($data['published_at']) : now();

        if (
            $current
            && $current->title === $data['title']
            && $current->content === $data['content']
            && optional($current->published_at)?->format('Y-m-d H:i') === $publishedAt->format('Y-m-d H:i')
        ) {
            return redirect()
                ->route('superadmin.pages.edit', ['slug' => $data['slug']])
                ->with('success', "O'zgarish topilmadi, yangi versiya yaratilmadi.");
        }

        $page = Page::query()->create([
            'slug' => $data['slug'],
            'locale' => 'uz',
            'title' => $data['title'],
            'content' => $data['content'],
            'version' => Page::latestVersionNumber($data['slug'], 'uz') + 1,
            'published_at' => $publishedAt,
            'updated_by' => $request->user()?->getKey(),
        ]);

        $audit->record('page.version.created', $page, null, ['slug' => $data['slug'], 'title' => $data['title'], 'version' => $page->version], 'info', $request, $data['title']);

        return redirect()
            ->route('superadmin.pages.edit', ['slug' => $data['slug']])
            ->with('success', 'Sahifa saqlandi.');
    }

    private function resolveSlug(?string $slug): string
    {
        return in_array($slug, Page::allowedSlugs(), true) ? $slug : Page::TERMS_OF_USE;
    }
}
