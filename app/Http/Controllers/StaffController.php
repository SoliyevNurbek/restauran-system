<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(): View
    {
        return view('staff.index', [
            'staffMembers' => Staff::latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('staff.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'role' => ['required', 'in:admin,waiter,cashier'],
        ]);

        Staff::create($data);

        return redirect()->route('staff.index')->with('success', 'Xodim muvaffaqiyatli yaratildi.');
    }

    public function show(Staff $staff): RedirectResponse
    {
        return redirect()->route('staff.edit', $staff);
    }

    public function edit(Staff $staff): View
    {
        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'role' => ['required', 'in:admin,waiter,cashier'],
        ]);

        $staff->update($data);

        return redirect()->route('staff.index')->with('success', 'Xodim muvaffaqiyatli yangilandi.');
    }

    public function destroy(Staff $staff): RedirectResponse
    {
        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Xodim muvaffaqiyatli o\'chirildi.');
    }
}
