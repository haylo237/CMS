@extends('layouts.app')
@section('page-title', 'Dashboard')

@section('content')
@php
    $currency = \App\Models\Setting::currencySymbol();
@endphp
{{-- Stats Row --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    @php
        $cards = [
            ['label' => 'Total Members',     'value' => $stats['total_members'],     'icon' => 'fa-users',         'color' => 'bg-blue-500'],
            ['label' => 'Branches',           'value' => $stats['total_branches'],    'icon' => 'fa-code-branch',   'color' => 'bg-teal-500'],
            ['label' => 'Upcoming Events',    'value' => $stats['upcoming_events']->count(), 'icon' => 'fa-calendar-days', 'color' => 'bg-violet-500'],
            ['label' => 'Pending Reviews',    'value' => $stats['reports_submitted'], 'icon' => 'fa-hourglass',     'color' => 'bg-amber-500'],
        ];
    @endphp

    @foreach($cards as $card)
        <div class="bg-white rounded-xl shadow-sm border p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl {{ $card['color'] }} text-white flex items-center justify-center text-xl">
                <i class="fa-solid {{ $card['icon'] }}"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($card['value']) }}</p>
                <p class="text-sm text-gray-500">{{ $card['label'] }}</p>
            </div>
        </div>
    @endforeach
</div>

{{-- Finance Summary --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <p class="text-sm text-gray-500 mb-1">Total Income</p>
        <p class="text-2xl font-bold text-green-600">{{ $currency }}{{ number_format($stats['total_income'], 2) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <p class="text-sm text-gray-500 mb-1">Total Expenses</p>
        <p class="text-2xl font-bold text-red-500">{{ $currency }}{{ number_format($stats['total_expense'], 2) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <p class="text-sm text-gray-500 mb-1">Net Balance</p>
        <p class="text-2xl font-bold {{ ($stats['total_income'] - $stats['total_expense']) >= 0 ? 'text-green-600' : 'text-red-500' }}">
            {{ $currency }}{{ number_format($stats['total_income'] - $stats['total_expense'], 2) }}
        </p>
    </div>
</div>

{{-- Charts Row --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Member Growth --}}
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <h2 class="font-semibold text-gray-700 mb-4">Member Growth (last 6 months)</h2>
        <canvas id="memberGrowthChart" height="140"></canvas>
    </div>

    {{-- Attendance Trend --}}
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <h2 class="font-semibold text-gray-700 mb-4">Attendance Trend (last 6 events)</h2>
        <canvas id="attendanceTrendChart" height="140"></canvas>
    </div>

    {{-- Finance Trend --}}
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <h2 class="font-semibold text-gray-700 mb-4">Finance Trend (last 6 months)</h2>
        <canvas id="financeTrendChart" height="140"></canvas>
    </div>

    {{-- Branch Distribution --}}
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <h2 class="font-semibold text-gray-700 mb-4">Members by Branch</h2>
        @if($branchStats->isEmpty())
            <p class="text-sm text-gray-400 text-center py-8">No branches yet.</p>
        @else
            <canvas id="branchChart" height="140"></canvas>
        @endif
    </div>
</div>

{{-- Upcoming Events & Announcements --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h2 class="font-semibold text-gray-700">Upcoming Events</h2>
            <a href="{{ route('events.index') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
        </div>
        <div class="divide-y">
            @forelse($stats['upcoming_events'] as $event)
                <a href="{{ route('events.show', $event) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50">
                    <div class="w-9 h-9 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600 shrink-0">
                        <i class="fa-solid fa-calendar-days text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $event->title }}</p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($event->date)->format('D, M d Y') }} · {{ $event->branch?->name ?? 'All branches' }}</p>
                    </div>
                </a>
            @empty
                <p class="px-5 py-6 text-sm text-gray-400 text-center">No upcoming events.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h2 class="font-semibold text-gray-700">Active Announcements</h2>
            <a href="{{ route('announcements.index') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
        </div>
        <div class="divide-y">
            @forelse($stats['active_announcements'] as $ann)
                <a href="{{ route('announcements.show', $ann) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50">
                    <div class="w-9 h-9 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600 shrink-0">
                        <i class="fa-solid fa-bullhorn text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $ann->title }}</p>
                        <p class="text-xs text-gray-400">{{ $ann->publishedBy?->full_name ?? '—' }} · {{ $ann->published_at?->diffForHumans() }}</p>
                    </div>
                </a>
            @empty
                <p class="px-5 py-6 text-sm text-gray-400 text-center">No active announcements.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Recent Reports & Transactions --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent Reports --}}
    <div class="bg-white rounded-xl shadow-sm border">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h2 class="font-semibold text-gray-700">Recent Reports</h2>
            <a href="{{ route('reports.index') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
        </div>
        <div class="divide-y">
            @forelse($stats['recent_reports'] as $report)
                <a href="{{ route('reports.show', $report) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $report->title }}</p>
                        <p class="text-xs text-gray-400">{{ $report->submittedBy->full_name }} · {{ $report->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full font-medium
                        {{ $report->status === 'reviewed' ? 'bg-green-100 text-green-700' :
                           ($report->status === 'submitted' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                        {{ ucfirst($report->status) }}
                    </span>
                </a>
            @empty
                <p class="px-5 py-6 text-sm text-gray-400 text-center">No reports yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="bg-white rounded-xl shadow-sm border">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h2 class="font-semibold text-gray-700">Recent Transactions</h2>
            <a href="{{ route('finance.index') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
        </div>
        <div class="divide-y">
            @forelse($stats['recent_transactions'] as $txn)
                <div class="flex items-center gap-3 px-5 py-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800">{{ $txn->category }}</p>
                        <p class="text-xs text-gray-400">{{ $txn->recordedBy->full_name }} · {{ $txn->transaction_date->format('d M Y') }}</p>
                    </div>
                    <span class="text-sm font-semibold {{ $txn->type === 'income' ? 'text-green-600' : 'text-red-500' }}">
                        {{ $txn->type === 'income' ? '+' : '-' }}{{ $currency }}{{ number_format($txn->amount, 2) }}
                    </span>
                </div>
            @empty
                <p class="px-5 py-6 text-sm text-gray-400 text-center">No transactions yet.</p>
            @endforelse
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const chartDefaults = { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } };

// Member Growth
new Chart(document.getElementById('memberGrowthChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($memberGrowth->pluck('month')) !!},
        datasets: [{ label: 'New Members', data: {!! json_encode($memberGrowth->pluck('total')) !!}, backgroundColor: '#6366f1', borderRadius: 6 }]
    },
    options: chartDefaults
});

// Attendance Trend
new Chart(document.getElementById('attendanceTrendChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($attendanceTrend->pluck('title')) !!},
        datasets: [{ label: 'Present', data: {!! json_encode($attendanceTrend->pluck('present_count')) !!}, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)', fill: true, tension: 0.4, pointRadius: 4 }]
    },
    options: { ...chartDefaults, plugins: { legend: { display: false } } }
});

// Finance Trend
@php
    $financeMonths  = $financeTrend->keys()->toArray();
    $incomeData     = [];
    $expenseData    = [];
    foreach ($financeMonths as $month) {
        $rows = $financeTrend[$month];
        $incomeData[]  = $rows->firstWhere('type', 'income')?->total  ?? 0;
        $expenseData[] = $rows->firstWhere('type', 'expense')?->total ?? 0;
    }
@endphp
new Chart(document.getElementById('financeTrendChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($financeMonths) !!},
        datasets: [
            { label: 'Income',  data: {!! json_encode($incomeData) !!},  backgroundColor: '#10b981', borderRadius: 4 },
            { label: 'Expense', data: {!! json_encode($expenseData) !!}, backgroundColor: '#ef4444', borderRadius: 4 }
        ]
    },
    options: { responsive: true, plugins: { legend: { display: true, position: 'top' } }, scales: { y: { beginAtZero: true } } }
});

// Branch Distribution
@if($branchStats->isNotEmpty())
new Chart(document.getElementById('branchChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($branchStats->pluck('name')) !!},
        datasets: [{ data: {!! json_encode($branchStats->pluck('members_count')) !!}, backgroundColor: ['#6366f1','#10b981','#f59e0b','#ef4444','#8b5cf6','#0ea5e9','#14b8a6'], borderWidth: 0 }]
    },
    options: { responsive: true, plugins: { legend: { display: true, position: 'right' } } }
});
@endif
</script>
@endsection

