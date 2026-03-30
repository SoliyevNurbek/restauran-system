<?php

namespace App\Http\Controllers;

use App\Models\Hall;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HallController extends Controller
{
    public function index(): View
    {
        return view('tables.index', ['halls' => Hall::orderBy('name')->paginate(12)]);
    }

    public function create(): View
    {
        return view('tables.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('halls', 'public');
        }
        $data['slug'] = Str::slug($data['name']);
        Hall::create($data);

        return redirect()->route('halls.index')->with('success', 'Zal yaratildi.');
    }

    public function show(Hall $hall): RedirectResponse
    {
        return redirect()->route('halls.edit', $hall);
    }

    public function edit(Hall $hall): View
    {
        return view('tables.edit', compact('hall'));
    }

    public function update(Request $request, Hall $hall): RedirectResponse
    {
        $data = $this->validateData($request, $hall);
        if ($request->hasFile('image')) {
            if ($hall->image) {
                Storage::disk('public')->delete($hall->image);
            }
            $data['image'] = $request->file('image')->store('halls', 'public');
        }
        $data['slug'] = Str::slug($data['name']);
        $hall->update($data);

        return redirect()->route('halls.index')->with('success', 'Zal yangilandi.');
    }

    public function destroy(Hall $hall): RedirectResponse
    {
        if ($hall->image) {
            Storage::disk('public')->delete($hall->image);
        }
        $hall->delete();

        return redirect()->route('halls.index')->with('success', 'Zal o\'chirildi.');
    }

    private function validateData(Request $request, ?Hall $hall = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:halls,name,'.($hall?->id ?? 'NULL').',id'],
            'capacity' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:Faol,Nofaol,Ta\'mirda'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);
    }
}
