@extends('layouts.app')
@section('page-title', $ministry->name)

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('ministries.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
    <h2 class="text-lg font-semibold">{{ $ministry->name }}</h2>
    @can('manage-ministries')
        <a href="{{ route('ministries.edit', $ministry) }}" class="ml-auto text-sm bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 px-3 py-1.5 rounded-lg transition">Edit</a>
    @endcan
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Members ({{ $ministry->members->count() }})</h3>
        @forelse($ministry->members as $member)
            <div class="flex items-center justify-between py-2 border-b last:border-0 text-sm">
                <a href="{{ route('members.show', $member) }}" class="font-medium text-indigo-600 hover:underline">{{ $member->full_name }}</a>
                <span class="capitalize text-gray-400 text-xs">{{ $member->pivot->role }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-400">No members assigned.</p>
        @endforelse
    </div>

    <div class="bg-white rounded-xl border shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-500 uppercase">Recent Reports</h3>
            <a href="{{ route('reports.index', ['ministry_id' => $ministry->id]) }}" class="text-xs text-indigo-600 hover:underline">View all</a>
        </div>
        @forelse($ministry->reports as $report)
            <div class="py-2 border-b last:border-0 text-sm">
                <a href="{{ route('reports.show', $report) }}" class="font-medium text-indigo-600 hover:underline">{{ $report->title }}</a>
                <p class="text-xs text-gray-400">{{ $report->created_at->format('d M Y') }} · {{ ucfirst($report->status) }}</p>
            </div>
        @empty
            <p class="text-sm text-gray-400">No reports yet.</p>
        @endforelse
    </div>
</div>
@endsection
