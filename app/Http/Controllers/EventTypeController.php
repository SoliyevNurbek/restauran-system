<?php

namespace App\Http\Controllers;

use App\Models\EventType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        EventType::create($request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:event_types,name'],
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
        $eventType->update($request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:event_types,name,'.$eventType->id],
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
