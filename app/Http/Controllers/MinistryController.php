<?php

namespace App\Http\Controllers;

use App\Models\Ministry;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MinistryController extends Controller
{
    public function index(): View
    {
        $ministries = Ministry::withCount('members')->orderBy('name')->get();

        return view('ministries.index', compact('ministries'));
    }

    public function create(): View
    {
        return view('ministries.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:ministries,name',
            'description' => 'nullable|string',
        ]);

        $ministry = Ministry::create($validated);

        return redirect()->route('ministries.show', $ministry)
                         ->with('success', 'Ministry created successfully.');
    }

    public function show(Ministry $ministry): View
    {
        $ministry->load(['members', 'reports' => fn($q) => $q->latest()->limit(5)]);
        $members = Member::orderBy('first_name')->orderBy('last_name')->get();

        return view('ministries.show', compact('ministry', 'members'));
    }

    public function edit(Ministry $ministry): View
    {
        return view('ministries.edit', compact('ministry'));
    }

    public function update(Request $request, Ministry $ministry): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:ministries,name,' . $ministry->id,
            'description' => 'nullable|string',
        ]);

        $ministry->update($validated);

        return redirect()->route('ministries.show', $ministry)
                         ->with('success', 'Ministry updated.');
    }

    public function destroy(Ministry $ministry): RedirectResponse
    {
        $ministry->delete();

        return redirect()->route('ministries.index')
                         ->with('success', 'Ministry deleted.');
    }

    public function assignMember(Request $request, Ministry $ministry): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'role' => 'required|in:leader,assistant,member',
        ]);

        $ministry->members()->syncWithoutDetaching([
            $validated['member_id'] => ['role' => $validated['role']],
        ]);

        return back()->with('success', 'Ministry member updated.');
    }

    public function removeMember(Request $request, Ministry $ministry): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
        ]);

        $ministry->members()->detach($validated['member_id']);

        return back()->with('success', 'Ministry member removed.');
    }
}
