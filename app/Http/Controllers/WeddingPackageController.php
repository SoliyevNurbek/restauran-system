<?php

namespace App\Http\Controllers;

use App\Models\WeddingPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WeddingPackageController extends Controller
{
    public function index(): View
    {
        return view('menu-items.index', ['packages' => WeddingPackage::latest()->paginate(10)]);
    }

    public function create(): View
    {
        return view('menu-items.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('packages', 'public');
        }
        WeddingPackage::create($data);

        return redirect()->route('wedding-packages.index')->with('success', 'To\'y paketi yaratildi.');
    }

    public function show(WeddingPackage $weddingPackage): RedirectResponse
    {
        return redirect()->route('wedding-packages.edit', $weddingPackage);
    }

    public function edit(WeddingPackage $weddingPackage): View
    {
        return view('menu-items.edit', compact('weddingPackage'));
    }

    public function update(Request $request, WeddingPackage $weddingPackage): RedirectResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('image')) {
            if ($weddingPackage->image) {
                Storage::disk('public')->delete($weddingPackage->image);
            }
            $data['image'] = $request->file('image')->store('packages', 'public');
        }
        $weddingPackage->update($data);

        return redirect()->route('wedding-packages.index')->with('success', 'To\'y paketi yangilandi.');
    }

    public function destroy(WeddingPackage $weddingPackage): RedirectResponse
    {
        if ($weddingPackage->image) {
            Storage::disk('public')->delete($weddingPackage->image);
        }
        $weddingPackage->delete();

        return redirect()->route('wedding-packages.index')->with('success', 'To\'y paketi o\'chirildi.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price_per_person' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:Faol,Nofaol'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);
    }
}
