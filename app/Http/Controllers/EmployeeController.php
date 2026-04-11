<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $role = (string) $request->query('role', '');
        $status = (string) $request->query('status', '');

        return view('staff.index', [
            'employees' => Employee::query()
                ->when($search !== '', fn ($query) => $query->where('full_name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"))
                ->when($role !== '', fn ($query) => $query->where('role', $role))
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            'filters' => compact('search', 'role', 'status'),
            'roles' => $this->roles(),
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
            'Egasi',
            'Menejer',
            'Kassir',
            'Operator',
            'Administrator',
            'Oshpaz',
            'Ofitsiant',
            'Dekorator',
            'Texnik xodim',
        ];
    }
}
