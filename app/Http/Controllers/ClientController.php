<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));

        return view('customers.index', [
            'clients' => Client::withCount('bookings')
                ->with(['bookings' => fn ($query) => $query->with('eventType')->latest('event_date')->take(1)])
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($inner) use ($search) {
                        $inner->where('full_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            'filters' => compact('search'),
        ]);
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Client::create($this->validateData($request));

        return redirect()->route('clients.index')->with('success', 'Mijoz yaratildi.');
    }

    public function show(Client $client): View
    {
        return view('customers.show', [
            'client' => $client,
            'bookings' => $client->bookings()->with(['hall', 'eventType'])->latest()->paginate(10),
        ]);
    }

    public function edit(Client $client): View
    {
        return view('customers.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $client->update($this->validateData($request));

        return redirect()->route('clients.index')->with('success', 'Mijoz yangilandi.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Mijoz o\'chirildi.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'extra_phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'passport_info' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
