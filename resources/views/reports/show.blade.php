@extends('layouts.app')
@section('page-title', $report->title)

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('reports.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
    <h2 class="text-lg font-semibold flex-1">{{ $report->title }}</h2>

    <span class="text-xs px-2.5 py-1 rounded-full font-medium
        {{ $report->status === 'reviewed' ? 'bg-green-100 text-green-700' :
           ($report->status === 'submitted' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
        {{ ucfirst($report->status) }}
    </span>

    @can('update', $report)
        <a href="{{ route('reports.edit', $report) }}" class="text-sm bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 px-3 py-1.5 rounded-lg transition">Edit</a>
    @endcan

    @can('submit', $report)
        <form method="POST" action="{{ route('reports.submit', $report) }}">
            @csrf @method('PATCH')
            <button class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg transition">Submit</button>
        </form>
    @endcan

    @can('review', $report)
        <button onclick="document.getElementById('reviewModal').classList.remove('hidden')"
                class="text-sm bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg transition">
            Mark Reviewed
        </button>
    @endcan
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border shadow-sm p-6">
            <dl class="space-y-3 text-sm">
                <div><dt class="text-gray-400">Type</dt><dd class="font-medium capitalize">{{ $report->report_type }}</dd></div>
                @if($report->department)
                    <div><dt class="text-gray-400">Department</dt><dd><a href="{{ route('departments.show', $report->department) }}" class="text-indigo-600 hover:underline">{{ $report->department->name }}</a></dd></div>
                @endif
                @if($report->ministry)
                    <div><dt class="text-gray-400">Ministry</dt><dd><a href="{{ route('ministries.show', $report->ministry) }}" class="text-indigo-600 hover:underline">{{ $report->ministry->name }}</a></dd></div>
                @endif
                <div><dt class="text-gray-400">Reporting Period</dt>
                    <dd>{{ $report->reporting_period_start->format('d M Y') }} — {{ $report->reporting_period_end->format('d M Y') }}</dd>
                </div>
                <div><dt class="text-gray-400">Submitted By</dt><dd><a href="{{ route('members.show', $report->submittedBy) }}" class="text-indigo-600 hover:underline">{{ $report->submittedBy->full_name }}</a></dd></div>
                <div><dt class="text-gray-400">Created</dt><dd>{{ $report->created_at->format('d M Y, H:i') }}</dd></div>
            </dl>
        </div>

        @if($report->description)
            <div class="bg-white rounded-xl border shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Description</h3>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $report->description }}</p>
            </div>
        @endif

        @if($report->metadata)
            <div class="bg-white rounded-xl border shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Metadata</h3>
                <pre class="text-xs bg-gray-50 rounded p-3 overflow-auto">{{ json_encode($report->metadata, JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif
    </div>

    {{-- Review Info --}}
    @if($report->reviewed_by)
        <div class="bg-white rounded-xl border shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Review</h3>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-gray-400">Reviewed By</dt><dd class="font-medium">{{ $report->reviewedBy->full_name }}</dd></div>
                <div><dt class="text-gray-400">Reviewed At</dt><dd>{{ $report->reviewed_at->format('d M Y, H:i') }}</dd></div>
                @if($report->review_notes)
                    <div><dt class="text-gray-400">Notes</dt><dd class="whitespace-pre-wrap">{{ $report->review_notes }}</dd></div>
                @endif
            </dl>
        </div>
    @endif
</div>

{{-- Review Modal --}}
<div id="reviewModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-base font-semibold mb-4">Mark as Reviewed</h3>
        <form method="POST" action="{{ route('reports.review', $report) }}">
            @csrf @method('PATCH')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Review Notes (optional)</label>
                <textarea name="review_notes" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">Confirm Review</button>
                <button type="button" onclick="document.getElementById('reviewModal').classList.add('hidden')" class="text-sm text-gray-500 hover:underline">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
