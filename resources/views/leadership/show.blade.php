@extends('layouts.app')
@section('page-title', $leadership->name)

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('leadership.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
    <h2 class="text-lg font-semibold">{{ $leadership->name }}</h2>
    <span class="text-xs bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full border border-amber-100">Rank {{ $leadership->rank }}</span>
    @can('manage-leadership')
        <a href="{{ route('leadership.edit', $leadership) }}" class="ml-auto text-sm bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 px-3 py-1.5 rounded-lg transition">Edit</a>
    @endcan
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Members in this Role ({{ $leadership->members->count() }})</h3>
        @forelse($leadership->members as $member)
            <div class="flex items-center justify-between py-2 border-b last:border-0 text-sm">
                <a href="{{ route('members.show', $member) }}" class="font-medium text-indigo-600 hover:underline">{{ $member->full_name }}</a>
                <span class="text-gray-400 text-xs">{{ $member->pivot->assigned_at ?? '—' }}</span>
                @can('manage-leadership')
                    <form method="POST" action="{{ route('leadership.members.remove', $leadership) }}" class="inline">
                        @csrf @method('DELETE')
                        <input type="hidden" name="member_id" value="{{ $member->id }}">
                        <button class="text-gray-300 hover:text-red-500 transition ml-2"><i class="fa-solid fa-xmark"></i></button>
                    </form>
                @endcan
            </div>
        @empty
            <p class="text-sm text-gray-400 mb-4">No members assigned.</p>
        @endforelse

        @can('manage-leadership')
            <form method="POST" action="{{ route('leadership.members.assign', $leadership) }}" class="mt-4 flex gap-2 flex-wrap">
                @csrf
                <select name="member_id" required class="border rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Select member</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                    @endforeach
                </select>
                <input type="date" name="assigned_at" class="border rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <button type="submit" class="bg-amber-500 text-white text-sm px-3 py-1.5 rounded-lg hover:bg-amber-600 transition">Assign</button>
            </form>
        @endcan
    </div>
</div>
@endsection
