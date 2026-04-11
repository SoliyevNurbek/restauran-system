<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\LandingContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingContentController extends Controller
{
    public function edit(Request $request): View
    {
        $locale = in_array($request->query('lang', 'uz'), ['uz', 'uzc', 'ru', 'en'], true) ? $request->query('lang', 'uz') : 'uz';

        return view('superadmin.landing.edit', [
            'pageTitle' => 'Landing sahifani boshqarish',
            'locale' => $locale,
            'content' => LandingContent::firstOrNew(['locale' => $locale]),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'locale' => ['required', 'in:uz,uzc,ru,en'],
            'hero_badge' => ['nullable', 'string', 'max:255'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_text' => ['nullable', 'string', 'max:1000'],
            'hero_primary_cta' => ['nullable', 'string', 'max:255'],
            'hero_secondary_cta' => ['nullable', 'string', 'max:255'],
            'hero_microcopy' => ['nullable', 'string', 'max:255'],
            'final_title' => ['nullable', 'string', 'max:255'],
            'final_text' => ['nullable', 'string', 'max:1000'],
            'contact_title' => ['nullable', 'string', 'max:255'],
            'contact_text' => ['nullable', 'string', 'max:1000'],
        ]);

        LandingContent::updateOrCreate(
            ['locale' => $data['locale']],
            $data
        );

        return back()->with('success', 'Landing kontenti saqlandi.');
    }
}
