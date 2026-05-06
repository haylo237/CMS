@extends('layouts.app')
@section('page-title', 'Dashboard')

@section('content')
{{-- Stats Row --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    @php
        $cards = [
            ['label' => 'Total Members',     'value' => $stats['total_members'],     'icon' => 'fa-users',       'color' => 'bg-blue-500'],
            ['label' => 'Departments',        'value' => $stats['total_departments'], 'icon' => 'fa-building',    'color' => 'bg-indigo-500'],
            ['label' => 'Ministries',         'value' => $stats['total_ministries'],  'icon' => 'fa-hands-praying','color' => 'bg-purple-500'],
            ['label' => 'Pending Reviews',    'value' => $stats['reports_submitted'], 'icon' => 'fa-hourglass',   'color' => 'bg-amber-500'],
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
        <p class="text-2xl font-bold text-green-600">₦{{ number_format($stats['total_income'], 2) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <p class="text-sm text-gray-500 mb-1">Total Expenses</p>
        <p class="text-2xl font-bold text-red-500">₦{{ number_format($stats['total_expense'], 2) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <p class="text-sm text-gray-500 mb-1">Net Balance</p>
        <p class="text-2xl font-bold {{ ($stats['total_income'] - $stats['total_expense']) >= 0 ? 'text-green-600' : 'text-red-500' }}">
            ₦{{ number_format($stats['total_income'] - $stats['total_expense'], 2) }}
        </p>
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
                        {{ $txn->type === 'income' ? '+' : '-' }}₦{{ number_format($txn->amount, 2) }}
                    </span>
                </div>
            @empty
                <p class="px-5 py-6 text-sm text-gray-400 text-center">No transactions yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
