<?php

namespace App\Http\Controllers;

use App\Models\CostCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CostCategoryController extends Controller
{
    public function index(): View
    {
        return view('expenses.categories.index', [
            'categories' => CostCategory::latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('expenses.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        CostCategory::create($this->validateData($request));

        return redirect()->route('expenses.categories.index')->with('success', 'Xarajat kategoriyasi yaratildi.');
    }

    public function show(CostCategory $category): RedirectResponse
    {
        return redirect()->route('expenses.categories.edit', $category);
    }

    public function edit(CostCategory $category): View
    {
        return view('expenses.categories.edit', compact('category'));
    }

    public function update(Request $request, CostCategory $category): RedirectResponse
    {
        $category->update($this->validateData($request));

        return redirect()->route('expenses.categories.index')->with('success', 'Xarajat kategoriyasi yangilandi.');
    }

    public function destroy(CostCategory $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('expenses.categories.index')->with('success', 'Xarajat kategoriyasi o\'chirildi.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:kitchen,event,fixed'],
        ]);
    }
}
