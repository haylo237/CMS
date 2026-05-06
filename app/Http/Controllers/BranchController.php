<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CountryCode;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(): View
    {
        $branchId = auth()->user()?->isPastor() ? auth()->user()->pastoredBranchId() : null;

        $branches = Branch::withCount('members')
                          ->with(['pastor', 'parentBranch', 'countryCode'])
                          ->when($branchId, fn($q) => $q->where('id', $branchId))
                          ->latest()
                          ->paginate(12);
        return view('branches.index', compact('branches'));
    }

    public function create(): View
    {
        if (auth()->user()?->isPastor()) {
            abort(403);
        }

        $members  = Member::orderBy('first_name')->get();
        $branches = Branch::all();
        $countryCodes = CountryCode::where('is_active', true)->orderBy('country_name')->get();
        return view('branches.create', compact('members', 'branches', 'countryCodes'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()?->isPastor()) {
            abort(403);
        }

        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'address'          => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:100',
            'country_code_id'  => 'nullable|exists:country_codes,id',
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
        if (auth()->user()?->isPastor() && $branch->id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        $branch->load(['countryCode', 'pastor', 'parentBranch', 'subBranches', 'members', 'events' => fn($q) => $q->latest('date')->limit(5)]);
        return view('branches.show', compact('branch'));
    }

    public function edit(Branch $branch): View
    {
        if (auth()->user()?->isPastor() && $branch->id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        $members  = Member::orderBy('first_name')->get();
        $branches = Branch::where('id', '!=', $branch->id)->get();
        $countryCodes = CountryCode::where('is_active', true)->orderBy('country_name')->get();
        return view('branches.edit', compact('branch', 'members', 'branches', 'countryCodes'));
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        if (auth()->user()?->isPastor() && $branch->id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'address'          => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:100',
            'country_code_id'  => 'nullable|exists:country_codes,id',
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
        if (auth()->user()?->isPastor()) {
            abort(403);
        }

        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Branch deleted.');
    }
}
