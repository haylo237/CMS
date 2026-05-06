@extends('layouts.app')
@section('page-title', 'Leadership')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-semibold">Leadership Roles</h2>
    @can('manage-leadership')
        <a href="{{ route('leadership.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">+ Add Role</a>
    @endcan
</div>

<div class="bg-white rounded-xl border shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-5 py-3 text-left">Rank</th>
                <th class="px-5 py-3 text-left">Role</th>
                <th class="px-5 py-3 text-left">Description</th>
                <th class="px-5 py-3 text-left">Members</th>
                <th class="px-5 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($roles as $role)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-bold text-amber-600">{{ $role->rank }}</td>
                    <td class="px-5 py-3 font-medium">{{ $role->name }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $role->description ?? '—' }}</td>
                    <td class="px-5 py-3">{{ $role->members_count }}</td>
                    <td class="px-5 py-3 flex items-center gap-2">
                        <a href="{{ route('leadership.show', $role) }}" class="text-gray-400 hover:text-indigo-600 transition"><i class="fa-solid fa-eye"></i></a>
                        @can('manage-leadership')
                            <a href="{{ route('leadership.edit', $role) }}" class="text-gray-400 hover:text-amber-500 transition"><i class="fa-solid fa-pen-to-square"></i></a>
                            <form method="POST" action="{{ route('leadership.destroy', $role) }}" onsubmit="return confirm('Delete role?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-400 hover:text-red-500 transition"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">No roles yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
