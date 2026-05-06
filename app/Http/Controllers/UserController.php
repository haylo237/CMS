<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('member')->orderBy('role')->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $members = Member::doesntHave('user')->orderBy('first_name')->get();

        return view('users.create', compact('members'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id|unique:users,member_id',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
            'role'      => 'required|in:' . implode(',', User::ROLES),
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')
                         ->with('success', 'User account created.');
    }

    public function show(User $user): View
    {
        $user->load('member');

        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:' . implode(',', User::ROLES),
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.show', $user)
                         ->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('users.index')
                         ->with('success', 'User account removed.');
    }
}
