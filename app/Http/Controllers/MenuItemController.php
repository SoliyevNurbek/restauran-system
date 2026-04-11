<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MediaFile;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    public function index(): View
    {
        return view('menu-items.index', [
            'menuItems' => MenuItem::with('category')->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('menu-items.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        if ($request->hasFile('image')) {
            $data['image'] = null;
            $data['image_media_file_id'] = MediaFile::createFromUpload($request->file('image'))->getKey();
        }

        unset($data['image']);

        MenuItem::create($data);

        return redirect()->route('menu-items.index')->with('success', 'Menyu elementi muvaffaqiyatli yaratildi.');
    }

    public function show(MenuItem $menuItem): RedirectResponse
    {
        return redirect()->route('menu-items.edit', $menuItem);
    }

    public function edit(MenuItem $menuItem): View
    {
        return view('menu-items.edit', [
            'menuItem' => $menuItem,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $data = $this->validateData($request);

        if ($request->hasFile('image')) {
            MediaFile::query()->whereKey($menuItem->image_media_file_id)->delete();
            $data['image'] = null;
            $data['image_media_file_id'] = MediaFile::createFromUpload($request->file('image'))->getKey();
        }

        unset($data['image']);

        $menuItem->update($data);

        return redirect()->route('menu-items.index')->with('success', 'Menyu elementi muvaffaqiyatli yangilandi.');
    }

    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        MediaFile::query()->whereKey($menuItem->image_media_file_id)->delete();

        $menuItem->delete();

        return redirect()->route('menu-items.index')->with('success', 'Menyu elementi muvaffaqiyatli o\'chirildi.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,unavailable'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
    }
}
