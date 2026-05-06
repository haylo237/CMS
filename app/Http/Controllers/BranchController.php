<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(): View
    {
        $branches = Branch::withCount('members')
                          ->with(['pastor', 'parentBranch'])
                          ->latest()
                          ->paginate(12);
        return view('branches.index', compact('branches'));
    }

    public function create(): View
    {
        $members  = Member::orderBy('first_name')->get();
        $branches = Branch::all();
        return view('branches.create', compact('members', 'branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'address'          => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:100',
            'phone'            => 'nullable|string|max:30',
            'email'            => 'nullable|email|max:255',
            'pastor_id'        => 'nullable|exists:members,id',
            'parent_branch_id' => 'nullable|exists:branches,id',
            'description'      => 'nullable|string',
        ]);

        Branch::create($data);
        return redirect()->route('branches.index')->with('success', 'Branch created successfully.');
    }

    public function show(Branch $branch): View
    {
        $branch->load(['pastor', 'parentBranch', 'subBranches', 'members', 'events' => fn($q) => $q->latest('date')->limit(5)]);
        return view('branches.show', compact('branch'));
    }

    public function edit(Branch $branch): View
    {
        $members  = Member::orderBy('first_name')->get();
        $branches = Branch::where('id', '!=', $branch->id)->get();
        return view('branches.edit', compact('branch', 'members', 'branches'));
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'address'          => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:100',
            'phone'            => 'nullable|string|max:30',
            'email'            => 'nullable|email|max:255',
            'pastor_id'        => 'nullable|exists:members,id',
            'parent_branch_id' => 'nullable|exists:branches,id',
            'description'      => 'nullable|string',
        ]);

        $branch->update($data);
        return redirect()->route('branches.index')->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Branch deleted.');
    }
}
