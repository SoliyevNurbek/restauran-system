<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('categories.index', [
            'categories' => Category::latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        Category::create([
            ...$data,
            'slug' => Str::slug($data['name']),
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategoriya muvaffaqiyatli yaratildi.');
    }

    public function show(Category $category): RedirectResponse
    {
        return redirect()->route('categories.edit', $category);
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,'.$category->id],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $category->update([
            ...$data,
            'slug' => Str::slug($data['name']),
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategoriya muvaffaqiyatli yangilandi.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategoriya muvaffaqiyatli o\'chirildi.');
    }
}
