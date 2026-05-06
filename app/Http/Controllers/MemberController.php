<?php

namespace App\Http\Controllers;

use App\Models\CountryCode;
use App\Models\Member;
use App\Models\LeadershipRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $query = Member::with('countryCode');
        $branchId = auth()->user()?->isPastor() ? auth()->user()->pastoredBranchId() : null;

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

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
        if (auth()->user()?->isPastor() && !auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        $countryCodes = CountryCode::where('is_active', true)->orderBy('country_name')->get();

        return view('members.create', compact('countryCodes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'country_code_id' => 'nullable|exists:country_codes,id',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|unique:members,email',
            'gender'        => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date|before:today',
            'status'        => 'required|in:active,inactive,deceased,transferred',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'address'       => 'nullable|string',
        ]);

        if (auth()->user()?->isPastor()) {
            $validated['branch_id'] = auth()->user()->pastoredBranchId();
        }

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('member-photos', 'public');
        }

        $member = Member::create($validated);

        return redirect()->route('members.show', $member)
                         ->with('success', 'Member created successfully.');
    }

    public function show(Member $member): View
    {
        if (auth()->user()?->isPastor() && $member->branch_id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        $member->load(['countryCode', 'departments', 'ministries', 'leadershipRoles', 'user']);

        $leadershipRoles  = LeadershipRole::orderBy('rank')->get();

        return view('members.show', compact('member', 'leadershipRoles'));
    }

    public function edit(Member $member): View
    {
        if (auth()->user()?->isPastor() && $member->branch_id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        $member->load('leadershipRoles');
        $countryCodes = CountryCode::where('is_active', true)->orderBy('country_name')->get();
        $leadershipRoles = LeadershipRole::orderBy('rank')->get();

        return view('members.edit', compact('member', 'countryCodes', 'leadershipRoles'));
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        if (auth()->user()?->isPastor() && $member->branch_id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'country_code_id' => 'nullable|exists:country_codes,id',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|unique:members,email,' . $member->id,
            'gender'        => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date|before:today',
            'status'        => 'required|in:active,inactive,deceased,transferred',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'address'       => 'nullable|string',
        ]);

        if (auth()->user()?->isPastor()) {
            $validated['branch_id'] = auth()->user()->pastoredBranchId();
        }

        if ($request->hasFile('profile_photo')) {
            if ($member->profile_photo) {
                Storage::disk('public')->delete($member->profile_photo);
            }

            $validated['profile_photo'] = $request->file('profile_photo')->store('member-photos', 'public');
        }

        $member->update($validated);

        return redirect()->route('members.show', $member)
                         ->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        if (auth()->user()?->isPastor() && $member->branch_id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        if ($member->profile_photo) {
            Storage::disk('public')->delete($member->profile_photo);
        }

        $member->delete();

        return redirect()->route('members.index')
                         ->with('success', 'Member removed successfully.');
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
