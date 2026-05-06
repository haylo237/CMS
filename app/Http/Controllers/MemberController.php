<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Member;
use App\Models\Ministry;
use App\Models\LeadershipRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $query = Member::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('phone', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $members = $query->orderBy('first_name')->paginate(20)->withQueryString();

        return view('members.index', compact('members'));
    }

    public function create(): View
    {
        return view('members.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|unique:members,email',
            'gender'        => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date|before:today',
            'status'        => 'required|in:active,inactive,deceased,transferred',
            'address'       => 'nullable|string',
        ]);

        $member = Member::create($validated);

        return redirect()->route('members.show', $member)
                         ->with('success', 'Member created successfully.');
    }

    public function show(Member $member): View
    {
        $member->load(['departments', 'ministries', 'leadershipRoles', 'user']);

        $departments      = Department::orderBy('name')->get();
        $ministries       = Ministry::orderBy('name')->get();
        $leadershipRoles  = LeadershipRole::orderBy('rank')->get();

        return view('members.show', compact('member', 'departments', 'ministries', 'leadershipRoles'));
    }

    public function edit(Member $member): View
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|unique:members,email,' . $member->id,
            'gender'        => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date|before:today',
            'status'        => 'required|in:active,inactive,deceased,transferred',
            'address'       => 'nullable|string',
        ]);

        $member->update($validated);

        return redirect()->route('members.show', $member)
                         ->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $member->delete();

        return redirect()->route('members.index')
                         ->with('success', 'Member removed successfully.');
    }

    public function assignDepartment(Request $request, Member $member): RedirectResponse
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'role'          => 'required|in:head,assistant,member',
        ]);

        $member->departments()->syncWithoutDetaching([
            $request->department_id => ['role' => $request->role],
        ]);

        return back()->with('success', 'Department assigned.');
    }

    public function removeDepartment(Request $request, Member $member): RedirectResponse
    {
        $request->validate(['department_id' => 'required|exists:departments,id']);
        $member->departments()->detach($request->department_id);

        return back()->with('success', 'Department removed.');
    }

    public function assignMinistry(Request $request, Member $member): RedirectResponse
    {
        $request->validate([
            'ministry_id' => 'required|exists:ministries,id',
            'role'        => 'required|in:leader,assistant,member',
        ]);

        $member->ministries()->syncWithoutDetaching([
            $request->ministry_id => ['role' => $request->role],
        ]);

        return back()->with('success', 'Ministry assigned.');
    }

    public function removeMinistry(Request $request, Member $member): RedirectResponse
    {
        $request->validate(['ministry_id' => 'required|exists:ministries,id']);
        $member->ministries()->detach($request->ministry_id);

        return back()->with('success', 'Ministry removed.');
    }

    public function assignLeadership(Request $request, Member $member): RedirectResponse
    {
        $request->validate([
            'leadership_role_id' => 'required|exists:leadership_roles,id',
            'assigned_at'        => 'nullable|date',
        ]);

        $member->leadershipRoles()->syncWithoutDetaching([
            $request->leadership_role_id => ['assigned_at' => $request->assigned_at],
        ]);

        return back()->with('success', 'Leadership role assigned.');
    }

    public function removeLeadership(Request $request, Member $member): RedirectResponse
    {
        $request->validate(['leadership_role_id' => 'required|exists:leadership_roles,id']);
        $member->leadershipRoles()->detach($request->leadership_role_id);

        return back()->with('success', 'Leadership role removed.');
    }
}
