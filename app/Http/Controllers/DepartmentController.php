<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::withCount('members')->orderBy('name')->get();

        return view('departments.index', compact('departments'));
    }

    public function create(): View
    {
        return view('departments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:departments,name',
            'type'        => 'required|in:leadership,ministry,operations',
            'description' => 'nullable|string',
        ]);

        $department = Department::create($validated);

        return redirect()->route('departments.show', $department)
                         ->with('success', 'Department created successfully.');
    }

    public function show(Department $department): View
    {
        $department->load(['members', 'reports' => fn($q) => $q->latest()->limit(5)]);
        $members = Member::orderBy('first_name')->orderBy('last_name')->get();

        return view('departments.show', compact('department', 'members'));
    }

    public function edit(Department $department): View
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:departments,name,' . $department->id,
            'type'        => 'required|in:leadership,ministry,operations',
            'description' => 'nullable|string',
        ]);

        $department->update($validated);

        return redirect()->route('departments.show', $department)
                         ->with('success', 'Department updated.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        $department->delete();

        return redirect()->route('departments.index')
                         ->with('success', 'Department deleted.');
    }

    public function assignMember(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'role' => 'required|in:head,assistant,member',
        ]);

        $department->members()->syncWithoutDetaching([
            $validated['member_id'] => ['role' => $validated['role']],
        ]);

        return back()->with('success', 'Department member updated.');
    }

    public function removeMember(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
        ]);

        $department->members()->detach($validated['member_id']);

        return back()->with('success', 'Department member removed.');
    }
}
