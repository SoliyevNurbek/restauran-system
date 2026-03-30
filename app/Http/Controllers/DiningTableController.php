<?php

namespace App\Http\Controllers;

use App\Models\DiningTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiningTableController extends Controller
{
    public function index(): View
    {
        return view('tables.index', [
            'tables' => DiningTable::orderBy('table_number')->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('tables.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'table_number' => ['required', 'string', 'max:50', 'unique:dining_tables,table_number'],
            'status' => ['required', 'in:free,occupied'],
        ]);

        DiningTable::create($data);

        return redirect()->route('tables.index')->with('success', 'Stol muvaffaqiyatli yaratildi.');
    }

    public function show(DiningTable $table): RedirectResponse
    {
        return redirect()->route('tables.edit', $table);
    }

    public function edit(DiningTable $table): View
    {
        return view('tables.edit', compact('table'));
    }

    public function update(Request $request, DiningTable $table): RedirectResponse
    {
        $data = $request->validate([
            'table_number' => ['required', 'string', 'max:50', 'unique:dining_tables,table_number,'.$table->id],
            'status' => ['required', 'in:free,occupied'],
        ]);

        $table->update($data);

        return redirect()->route('tables.index')->with('success', 'Stol muvaffaqiyatli yangilandi.');
    }

    public function destroy(DiningTable $table): RedirectResponse
    {
        $table->delete();

        return redirect()->route('tables.index')->with('success', 'Stol muvaffaqiyatli o\'chirildi.');
    }
}
