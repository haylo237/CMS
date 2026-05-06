<?php

namespace App\Http\Controllers;

use App\Models\LeadershipRole;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadershipController extends Controller
{
    public function index(): View
    {
        $roles = LeadershipRole::withCount('members')->orderBy('rank')->get();

        return view('leadership.index', compact('roles'));
    }

    public function create(): View
    {
        return view('leadership.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:leadership_roles,name',
            'rank'        => 'required|integer|min:1|max:255',
            'description' => 'nullable|string',
        ]);

        LeadershipRole::create($validated);

        return redirect()->route('leadership.index')
                         ->with('success', 'Leadership role created.');
    }

    public function show(LeadershipRole $leadership): View
    {
        $leadership->load(['members' => fn($q) => $q->orderBy('first_name')]);
        $members = Member::orderBy('first_name')->get();

        return view('leadership.show', compact('leadership', 'members'));
    }

    public function edit(LeadershipRole $leadership): View
    {
        return view('leadership.edit', compact('leadership'));
    }

    public function update(Request $request, LeadershipRole $leadership): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:leadership_roles,name,' . $leadership->id,
            'rank'        => 'required|integer|min:1|max:255',
            'description' => 'nullable|string',
        ]);

        $leadership->update($validated);

        return redirect()->route('leadership.index')
                         ->with('success', 'Leadership role updated.');
    }

    public function destroy(LeadershipRole $leadership): RedirectResponse
    {
        $leadership->delete();

        return redirect()->route('leadership.index')
                         ->with('success', 'Leadership role deleted.');
    }

    public function assignMember(Request $request, LeadershipRole $leadership): RedirectResponse
    {
        $request->validate([
            'member_id'   => 'required|exists:members,id',
            'assigned_at' => 'nullable|date',
        ]);

        $leadership->members()->syncWithoutDetaching([
            $request->member_id => ['assigned_at' => $request->assigned_at],
        ]);

        return back()->with('success', 'Member assigned to leadership role.');
    }

    public function removeMember(Request $request, LeadershipRole $leadership): RedirectResponse
    {
        $request->validate(['member_id' => 'required|exists:members,id']);
        $leadership->members()->detach($request->member_id);

        return back()->with('success', 'Member removed from leadership role.');
    }
}
