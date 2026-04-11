<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View|Response
    {
        abort_unless(in_array($slug, Page::allowedSlugs(), true), 404);

        $page = Page::currentPublished($slug, 'uz');

        abort_if(! $page, 404);

        return view('pages.show', [
            'page' => $page,
        ]);
    }
}
