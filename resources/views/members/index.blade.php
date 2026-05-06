@extends('layouts.app')
@section('page-title', 'Members')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-semibold">All Members</h2>
    @can('manage-members')
        <a href="{{ route('members.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Add Member
        </a>
    @endcan
</div>

{{-- Filters --}}
<form method="GET" class="bg-white rounded-xl border shadow-sm p-4 mb-6 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, phone…"
               class="border rounded-lg px-3 py-2 text-sm w-56 focus:outline-none focus:ring-2 focus:ring-indigo-400">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Status</label>
        <select name="status" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">All Statuses</option>
            @foreach(['active','inactive','deceased','transferred'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Gender</label>
        <select name="gender" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">All Genders</option>
            @foreach(['male','female','other'] as $g)
                <option value="{{ $g }}" @selected(request('gender') === $g)>{{ ucfirst($g) }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700 transition">Filter</button>
    <a href="{{ route('members.index') }}" class="text-sm text-gray-500 hover:underline py-2">Clear</a>
</form>

{{-- Table --}}
<div class="bg-white rounded-xl border shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-5 py-3 text-left">Name</th>
                <th class="px-5 py-3 text-left">Email</th>
                <th class="px-5 py-3 text-left">Phone</th>
                <th class="px-5 py-3 text-left">Gender</th>
                <th class="px-5 py-3 text-left">Status</th>
                <th class="px-5 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($members as $member)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium">
                        <a href="{{ route('members.show', $member) }}" class="text-indigo-600 hover:underline">
                            {{ $member->full_name }}
                        </a>
                    </td>
                    <td class="px-5 py-3 text-gray-500">{{ $member->email ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $member->phone ?? '—' }}</td>
                    <td class="px-5 py-3 capitalize text-gray-500">{{ $member->gender ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $member->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ ucfirst($member->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 flex items-center gap-2">
                        <a href="{{ route('members.show', $member) }}" class="text-gray-400 hover:text-indigo-600 transition" title="View">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        @can('manage-members')
                            <a href="{{ route('members.edit', $member) }}" class="text-gray-400 hover:text-amber-500 transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form method="POST" action="{{ route('members.destroy', $member) }}" onsubmit="return confirm('Delete this member?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-500 transition" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No members found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-4 border-t">{{ $members->links() }}</div>
</div>
@endsection
