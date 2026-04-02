<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(): View
    {
        return view('finance-expenses.index', [
            'expenses' => Expense::with('category')->latest('expense_date')->latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('finance-expenses.create', [
            'categories' => ExpenseCategory::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Expense::create($this->validateExpense($request));

        return redirect()->route('inventory-expenses.index')->with('success', 'Xarajat saqlandi.');
    }

    public function show(Expense $inventory_expense): RedirectResponse
    {
        return redirect()->route('inventory-expenses.edit', ['inventory_expense' => $inventory_expense]);
    }

    public function edit(Expense $inventory_expense): View
    {
        return view('finance-expenses.edit', [
            'expense' => $inventory_expense,
            'categories' => ExpenseCategory::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Expense $inventory_expense): RedirectResponse
    {
        $inventory_expense->update($this->validateExpense($request));

        return redirect()->route('inventory-expenses.index')->with('success', 'Xarajat yangilandi.');
    }

    public function destroy(Expense $inventory_expense): RedirectResponse
    {
        $inventory_expense->delete();

        return redirect()->route('inventory-expenses.index')->with('success', 'Xarajat o\'chirildi.');
    }

    private function validateExpense(Request $request): array
    {
        return $request->validate([
            'expense_category_id' => ['nullable', 'exists:expense_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'expense_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
