@extends('layouts.app')
@section('page-title', 'Reports')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-semibold">All Reports</h2>
    @can('create', App\Models\Report::class)
        <a href="{{ route('reports.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">+ New Report</a>
    @endcan
</div>

{{-- Filters --}}
<form method="GET" class="bg-white rounded-xl border shadow-sm p-4 mb-6 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Type</label>
        <select name="type" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">All Types</option>
            @foreach(['department','ministry','finance'] as $t)
                <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Status</label>
        <select name="status" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">All Statuses</option>
            @foreach(['draft','submitted','reviewed'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Department</label>
        <select name="department_id" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Ministry</label>
        <select name="ministry_id" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">All Ministries</option>
            @foreach($ministries as $min)
                <option value="{{ $min->id }}" @selected(request('ministry_id') == $min->id)>{{ $min->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">From</label>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">To</label>
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
    </div>
    <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700 transition">Filter</button>
    <a href="{{ route('reports.index') }}" class="text-sm text-gray-500 hover:underline py-2">Clear</a>
</form>

<div class="bg-white rounded-xl border shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-5 py-3 text-left">Title</th>
                <th class="px-5 py-3 text-left">Type</th>
                <th class="px-5 py-3 text-left">Submitted By</th>
                <th class="px-5 py-3 text-left">Period</th>
                <th class="px-5 py-3 text-left">Status</th>
                <th class="px-5 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($reports as $report)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium">
                        <a href="{{ route('reports.show', $report) }}" class="text-indigo-600 hover:underline">{{ $report->title }}</a>
                    </td>
                    <td class="px-5 py-3 capitalize text-gray-500">{{ $report->report_type }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $report->submittedBy->full_name }}</td>
                    <td class="px-5 py-3 text-gray-400 text-xs">
                        {{ $report->reporting_period_start->format('d M Y') }} — {{ $report->reporting_period_end->format('d M Y') }}
                    </td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $report->status === 'reviewed' ? 'bg-green-100 text-green-700' :
                               ($report->status === 'submitted' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ ucfirst($report->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <a href="{{ route('reports.show', $report) }}" class="text-gray-400 hover:text-indigo-600 transition"><i class="fa-solid fa-eye"></i></a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No reports found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-4 border-t">{{ $reports->links() }}</div>
</div>
@endsection
