<?php

namespace App\Http\Controllers;

use App\Models\MediaFile;
use App\Models\WeddingPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            $data['image'] = null;
            $data['image_media_file_id'] = MediaFile::createFromUpload($request->file('image'))->getKey();
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

        if ($request->boolean('remove_image') && $weddingPackage->image_media_file_id) {
            MediaFile::query()->whereKey($weddingPackage->image_media_file_id)->delete();
            $data['image'] = null;
            $data['image_media_file_id'] = null;
        }

        if ($request->hasFile('image')) {
            MediaFile::query()->whereKey($weddingPackage->image_media_file_id)->delete();
            $data['image'] = null;
            $data['image_media_file_id'] = MediaFile::createFromUpload($request->file('image'))->getKey();
        }

        $weddingPackage->update($data);
        $this->removeSelectedGalleryImages($request, $weddingPackage);
        $this->storeGalleryImages($request, $weddingPackage);

        return redirect()->route('wedding-packages.index')->with('success', 'To\'y paketi yangilandi.');
    }

    public function destroy(WeddingPackage $weddingPackage): RedirectResponse
    {
        MediaFile::query()->whereKey($weddingPackage->image_media_file_id)->delete();

        foreach ($weddingPackage->images as $image) {
            MediaFile::query()->whereKey($image->media_file_id)->delete();
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
            $mediaFile = MediaFile::createFromUpload($imageFile);

            $weddingPackage->images()->create([
                'image_path' => $mediaFile->filename,
                'media_file_id' => $mediaFile->getKey(),
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
            MediaFile::query()->whereKey($image->media_file_id)->delete();
            $image->delete();
        }
    }
}
