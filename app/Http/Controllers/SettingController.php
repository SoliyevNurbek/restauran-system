<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('settings.edit', [
            'setting' => Setting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $setting = Setting::current();

        $data = $request->validate([
            'restaurant_name' => ['required', 'string', 'max:255'],
            'theme_preference' => ['required', 'in:light,dark'],
            'logo' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('logo')) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }

            $data['logo_path'] = $request->file('logo')->store('branding', 'public');
        }

        unset($data['logo']);

        $setting->update($data);

        return redirect()->route('settings.edit')->with('success', 'Sozlamalar muvaffaqiyatli yangilandi.');
    }
}
