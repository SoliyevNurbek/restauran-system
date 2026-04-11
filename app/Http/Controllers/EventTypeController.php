<?php

namespace App\Http\Controllers;

use App\Models\EventType;
use App\Support\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EventTypeController extends Controller
{
    public function index(): View
    {
        return view('categories.index', ['eventTypes' => EventType::latest()->paginate(10)]);
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $tenantId = TenantContext::id();

        EventType::create($request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('event_types', 'name')->where('venue_connection_id', $tenantId)],
            'description' => ['nullable', 'string', 'max:2000'],
        ]));

        return redirect()->route('event-types.index')->with('success', 'Tadbir turi yaratildi.');
    }

    public function show(EventType $eventType): RedirectResponse
    {
        return redirect()->route('event-types.edit', $eventType);
    }

    public function edit(EventType $eventType): View
    {
        return view('categories.edit', compact('eventType'));
    }

    public function update(Request $request, EventType $eventType): RedirectResponse
    {
        $tenantId = TenantContext::id();

        $eventType->update($request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('event_types', 'name')->ignore($eventType->id)->where('venue_connection_id', $tenantId)],
            'description' => ['nullable', 'string', 'max:2000'],
        ]));

        return redirect()->route('event-types.index')->with('success', 'Tadbir turi yangilandi.');
    }

    public function destroy(EventType $eventType): RedirectResponse
    {
        $eventType->delete();

        return redirect()->route('event-types.index')->with('success', 'Tadbir turi o\'chirildi.');
    }
}
