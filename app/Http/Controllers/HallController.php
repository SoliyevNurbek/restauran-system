<?php

namespace App\Http\Controllers;

use App\Models\Hall;
use App\Models\MediaFile;
use App\Support\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HallController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $status = (string) $request->query('status', '');

        return view('tables.index', [
            'halls' => Hall::query()
                ->withCount('bookings')
                ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->orderBy('name')
                ->paginate(12)
                ->withQueryString(),
            'filters' => compact('search', 'status'),
        ]);
    }

    public function create(): View
    {
        return view('tables.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        if ($request->hasFile('image')) {
            $data['image'] = null;
            $data['image_media_file_id'] = MediaFile::createFromUpload($request->file('image'))->getKey();
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
            MediaFile::query()->whereKey($hall->image_media_file_id)->delete();
            $data['image'] = null;
            $data['image_media_file_id'] = MediaFile::createFromUpload($request->file('image'))->getKey();
        }
        $data['slug'] = Str::slug($data['name']);
        $hall->update($data);

        return redirect()->route('halls.index')->with('success', 'Zal yangilandi.');
    }

    public function destroy(Hall $hall): RedirectResponse
    {
        MediaFile::query()->whereKey($hall->image_media_file_id)->delete();
        $hall->delete();

        return redirect()->route('halls.index')->with('success', 'Zal o\'chirildi.');
    }

    private function validateData(Request $request, ?Hall $hall = null): array
    {
        $tenantId = TenantContext::id();

        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('halls', 'name')
                    ->ignore($hall?->id)
                    ->where('venue_connection_id', $tenantId),
            ],
            'capacity' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:Faol,Nofaol,Ta\'mirda'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
    }
}
