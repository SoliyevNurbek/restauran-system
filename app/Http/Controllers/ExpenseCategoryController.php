<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseCategoryController extends Controller
{
    public function index(): View
    {
        return view('finance-expense-categories.index', [
            'categories' => ExpenseCategory::withCount('expenses')->latest()->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('finance-expense-categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        ExpenseCategory::create($this->validateCategory($request));

        return redirect()->route('inventory-expense-categories.index')->with('success', 'Xarajat kategoriyasi yaratildi.');
    }

    public function show(ExpenseCategory $expenseCategory): RedirectResponse
    {
        return redirect()->route('inventory-expense-categories.edit', $expenseCategory);
    }

    public function edit(ExpenseCategory $expenseCategory): View
    {
        return view('finance-expense-categories.edit', compact('expenseCategory'));
    }

    public function update(Request $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        $expenseCategory->update($this->validateCategory($request));

        return redirect()->route('inventory-expense-categories.index')->with('success', 'Xarajat kategoriyasi yangilandi.');
    }

    public function destroy(ExpenseCategory $expenseCategory): RedirectResponse
    {
        if ($expenseCategory->expenses()->exists()) {
            return back()->withErrors([
                'delete' => 'Bu kategoriya xarajatlarga biriktirilgan. Avval xarajatlarni o\'zgartiring.',
            ]);
        }

        $expenseCategory->delete();

        return redirect()->route('inventory-expense-categories.index')->with('success', 'Xarajat kategoriyasi o\'chirildi.');
    }

    private function validateCategory(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
