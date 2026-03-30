<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        return view('staff.index', [
            'employees' => Employee::latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('staff.create', ['roles' => $this->roles()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Employee::create($this->validateData($request));

        return redirect()->route('employees.index')->with('success', 'Xodim yaratildi.');
    }

    public function show(Employee $employee): RedirectResponse
    {
        return redirect()->route('employees.edit', $employee);
    }

    public function edit(Employee $employee): View
    {
        return view('staff.edit', [
            'employee' => $employee,
            'roles' => $this->roles(),
        ]);
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $employee->update($this->validateData($request));

        return redirect()->route('employees.index')->with('success', 'Xodim yangilandi.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Xodim o\'chirildi.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:'.implode(',', $this->roles())],
            'phone' => ['nullable', 'string', 'max:40'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:Faol,Nofaol'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function roles(): array
    {
        return [
            'Administrator',
            'Menejer',
            'Kassir',
            'Oshpaz',
            'Ofitsiant',
            'Dekorator',
            'Texnik xodim',
        ];
    }
}
