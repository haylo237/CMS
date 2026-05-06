<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CountryCode;
use App\Models\Division;
use App\Models\Member;
use App\Models\Region;
use App\Models\Subdivision;
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

        $cameroonCodeId = CountryCode::where('iso_code', 'CM')->value('id');
        $regions = Region::where('country_code_id', $cameroonCodeId)->orderBy('item_number')->get();
        $divisions = Division::whereIn('region_id', $regions->pluck('id'))->orderBy('item_number')->get();
        $subdivisions = Subdivision::whereIn('division_id', $divisions->pluck('id'))->orderBy('item_number')->get();

        return view('branches.create', compact('members', 'branches', 'countryCodes', 'regions', 'divisions', 'subdivisions'));
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
            'region_id'        => 'nullable|exists:regions,id',
            'division_id'      => 'nullable|exists:divisions,id',
            'subdivision_id'   => 'nullable|exists:subdivisions,id',
            'country_code_id'  => 'nullable|exists:country_codes,id',
            'phone'            => 'nullable|string|max:30',
            'email'            => 'nullable|email|max:255',
            'pastor_id'        => 'nullable|exists:members,id',
            'parent_branch_id' => 'nullable|exists:branches,id',
            'description'      => 'nullable|string',
        ]);

        $this->validateAdministrativeHierarchy($data, $request);

        Branch::create($data);
        return redirect()->route('branches.index')->with('success', 'Branch created successfully.');
    }

    public function show(Branch $branch): View
    {
        if (auth()->user()?->isPastor() && $branch->id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        $branch->load(['countryCode', 'regionRef', 'divisionRef', 'subdivisionRef', 'pastor', 'parentBranch', 'subBranches', 'members', 'events' => fn($q) => $q->latest('date')->limit(5)]);
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

        $cameroonCodeId = CountryCode::where('iso_code', 'CM')->value('id');
        $regions = Region::where('country_code_id', $cameroonCodeId)->orderBy('item_number')->get();
        $divisions = Division::whereIn('region_id', $regions->pluck('id'))->orderBy('item_number')->get();
        $subdivisions = Subdivision::whereIn('division_id', $divisions->pluck('id'))->orderBy('item_number')->get();

        return view('branches.edit', compact('branch', 'members', 'branches', 'countryCodes', 'regions', 'divisions', 'subdivisions'));
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
            'region_id'        => 'nullable|exists:regions,id',
            'division_id'      => 'nullable|exists:divisions,id',
            'subdivision_id'   => 'nullable|exists:subdivisions,id',
            'country_code_id'  => 'nullable|exists:country_codes,id',
            'phone'            => 'nullable|string|max:30',
            'email'            => 'nullable|email|max:255',
            'pastor_id'        => 'nullable|exists:members,id',
            'parent_branch_id' => 'nullable|exists:branches,id',
            'description'      => 'nullable|string',
        ]);

        $this->validateAdministrativeHierarchy($data, $request);

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

    private function validateAdministrativeHierarchy(array &$data, Request $request): void
    {
        $cameroonCodeId = CountryCode::where('iso_code', 'CM')->value('id');
        $isCameroon = !empty($data['country_code_id']) && (int) $data['country_code_id'] === (int) $cameroonCodeId;

        if (!$isCameroon) {
            $data['region_id'] = null;
            $data['division_id'] = null;
            $data['subdivision_id'] = null;
            $data['region'] = null;
            $data['division'] = null;
            $data['subdivision'] = null;
            return;
        }

        $request->validate([
            'region_id' => 'required|exists:regions,id',
            'division_id' => 'required|exists:divisions,id',
            'subdivision_id' => 'nullable|exists:subdivisions,id',
        ]);

        $region = Region::where('id', $data['region_id'])->where('country_code_id', $cameroonCodeId)->first();
        if (!$region) {
            abort(422, 'Selected region is invalid for Cameroon.');
        }

        $division = Division::where('id', $data['division_id'])->where('region_id', $region->id)->first();
        if (!$division) {
            abort(422, 'Selected division does not belong to the selected region.');
        }

        $subdivision = null;
        if (!empty($data['subdivision_id'])) {
            $subdivision = Subdivision::where('id', $data['subdivision_id'])->where('division_id', $division->id)->first();
            if (!$subdivision) {
                abort(422, 'Selected subdivision does not belong to the selected division.');
            }
        }

        // Keep legacy text columns synchronized for existing UI/report usages.
        $data['region'] = $region->name;
        $data['division'] = $division->name;
        $data['subdivision'] = $subdivision?->name;
    }
}
