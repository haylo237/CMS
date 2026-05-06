@extends('layouts.app')
@section('page-title', 'Users')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-semibold">User Accounts</h2>
    <a href="{{ route('users.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">+ Create User</a>
</div>

<div class="bg-white rounded-xl border shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-5 py-3 text-left">Member</th>
                <th class="px-5 py-3 text-left">Email</th>
                <th class="px-5 py-3 text-left">Role</th>
                <th class="px-5 py-3 text-left">Created</th>
                <th class="px-5 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium">
                        <a href="{{ route('members.show', $user->member) }}" class="text-indigo-600 hover:underline">{{ $user->member->full_name }}</a>
                    </td>
                    <td class="px-5 py-3 text-gray-500">{{ $user->email }}</td>
                    <td class="px-5 py-3 capitalize text-xs">
                        <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-full">{{ str_replace('_', ' ', $user->role) }}</span>
                    </td>
                    <td class="px-5 py-3 text-gray-400">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-3 flex items-center gap-2">
                        <a href="{{ route('users.edit', $user) }}" class="text-gray-400 hover:text-amber-500 transition"><i class="fa-solid fa-pen-to-square"></i></a>
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Remove user account?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-400 hover:text-red-500 transition"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">No users yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-4 border-t">{{ $users->links() }}</div>
</div>
@endsection
