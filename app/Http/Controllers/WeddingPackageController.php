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
        return view('menu-items.index', [
            'packages' => WeddingPackage::with('images')->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('menu-items.create', [
            'packageNameOptions' => WeddingPackage::NAME_OPTIONS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('packages', 'public');
        }

        $weddingPackage = WeddingPackage::create($data);
        $this->storeGalleryImages($request, $weddingPackage);

        return redirect()->route('wedding-packages.index')->with('success', 'To\'y paketi yaratildi.');
    }

    public function show(WeddingPackage $weddingPackage): RedirectResponse
    {
        return redirect()->route('wedding-packages.edit', $weddingPackage);
    }

    public function edit(WeddingPackage $weddingPackage): View
    {
        return view('menu-items.edit', [
            'weddingPackage' => $weddingPackage,
            'packageNameOptions' => WeddingPackage::NAME_OPTIONS,
        ]);
    }

    public function update(Request $request, WeddingPackage $weddingPackage): RedirectResponse
    {
        $data = $this->validateData($request);

        if ($request->boolean('remove_image') && $weddingPackage->image) {
            Storage::disk('public')->delete($weddingPackage->image);
            $data['image'] = null;
        }

        if ($request->hasFile('image')) {
            if ($weddingPackage->image) {
                Storage::disk('public')->delete($weddingPackage->image);
            }
            $data['image'] = $request->file('image')->store('packages', 'public');
        }

        $weddingPackage->update($data);
        $this->removeSelectedGalleryImages($request, $weddingPackage);
        $this->storeGalleryImages($request, $weddingPackage);

        return redirect()->route('wedding-packages.index')->with('success', 'To\'y paketi yangilandi.');
    }

    public function destroy(WeddingPackage $weddingPackage): RedirectResponse
    {
        if ($weddingPackage->image) {
            Storage::disk('public')->delete($weddingPackage->image);
        }

        foreach ($weddingPackage->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $weddingPackage->delete();

        return redirect()->route('wedding-packages.index')->with('success', 'To\'y paketi o\'chirildi.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'in:'.implode(',', WeddingPackage::NAME_OPTIONS)],
            'price_per_person' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:Faol,Nofaol'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_gallery_images' => ['nullable', 'array'],
            'remove_gallery_images.*' => ['integer'],
            'remove_image' => ['nullable', 'boolean'],
        ]);
    }

    private function storeGalleryImages(Request $request, WeddingPackage $weddingPackage): void
    {
        if (! $request->hasFile('gallery_images')) {
            return;
        }

        $nextSortOrder = (int) $weddingPackage->images()->max('sort_order');

        foreach ($request->file('gallery_images') as $imageFile) {
            $nextSortOrder++;

            $weddingPackage->images()->create([
                'image_path' => $imageFile->store('packages/gallery', 'public'),
                'sort_order' => $nextSortOrder,
            ]);
        }
    }

    private function removeSelectedGalleryImages(Request $request, WeddingPackage $weddingPackage): void
    {
        $imageIds = collect($request->input('remove_gallery_images', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($imageIds === []) {
            return;
        }

        $images = $weddingPackage->images()->whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
    }
}
