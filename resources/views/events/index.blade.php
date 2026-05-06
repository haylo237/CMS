@extends('layouts.app')

@section('title', 'Events')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Events</h1>
    <a href="{{ route('events.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        <i class="fa-solid fa-plus"></i> New Event
    </a>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

{{-- Filters --}}
<form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Branch</label>
        <select name="branch_id" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
            <option value="">All branches</option>
            @foreach($branches as $b)
                <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Type</label>
        <select name="type" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
            <option value="">All types</option>
            @foreach(['service','meeting','special','prayer','outreach'] as $t)
                <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">From</label>
        <input type="date" name="from" value="{{ request('from') }}" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">To</label>
        <input type="date" name="to" value="{{ request('to') }}" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
    </div>
    <button type="submit" class="bg-indigo-600 text-white px-4 py-1.5 rounded-lg text-sm">Filter</button>
    <a href="{{ route('events.index') }}" class="text-gray-500 text-sm px-3 py-1.5 border border-gray-300 rounded-lg hover:bg-gray-50">Clear</a>
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Title</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Type</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Branch</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Date</th>
                <th class="px-4 py-3 text-left font-medium text-gray-600">Attendances</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($events as $event)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $event->title }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-1 rounded-full font-medium
                            {{ $event->type === 'service' ? 'bg-blue-50 text-blue-700' : '' }}
                            {{ $event->type === 'meeting' ? 'bg-yellow-50 text-yellow-700' : '' }}
                            {{ $event->type === 'special' ? 'bg-purple-50 text-purple-700' : '' }}
                            {{ $event->type === 'prayer' ? 'bg-green-50 text-green-700' : '' }}
                            {{ $event->type === 'outreach' ? 'bg-orange-50 text-orange-700' : '' }}
                        ">{{ ucfirst($event->type) }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $event->branch?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $event->attendances_count }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('events.show', $event) }}" class="text-indigo-600 hover:underline mr-2">View</a>
                        <a href="{{ route('events.edit', $event) }}" class="text-yellow-600 hover:underline mr-2">Edit</a>
                        <form method="POST" action="{{ route('events.destroy', $event) }}" class="inline" onsubmit="return confirm('Delete event?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">No events found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $events->links() }}</div>
@endsection
