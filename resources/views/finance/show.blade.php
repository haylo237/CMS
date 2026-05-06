@extends('layouts.app')
@section('page-title', 'Transaction Detail')

@section('content')
@php($currency = \App\Models\Setting::currencySymbol())
<div class="max-w-xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('finance.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
        <h2 class="text-lg font-semibold">Transaction #{{ $finance->id }}</h2>
        @can('manage-finance')
            <a href="{{ route('finance.edit', $finance) }}" class="ml-auto text-sm bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 px-3 py-1.5 rounded-lg transition">Edit</a>
        @endcan
    </div>
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <dl class="space-y-4 text-sm">
            <div class="flex justify-between">
                <dt class="text-gray-500">Amount</dt>
                <dd class="font-bold text-lg {{ $finance->type === 'income' ? 'text-green-600' : 'text-red-500' }}">
                    {{ $finance->type === 'income' ? '+' : '-' }}{{ $currency }}{{ number_format($finance->amount, 2) }}
                </dd>
            </div>
            <div><dt class="text-gray-400">Type</dt><dd class="capitalize font-medium">{{ $finance->type }}</dd></div>
            <div><dt class="text-gray-400">Category</dt><dd>{{ $finance->category }}</dd></div>
            <div><dt class="text-gray-400">Date</dt><dd>{{ $finance->transaction_date->format('d M Y') }}</dd></div>
            <div><dt class="text-gray-400">Reference</dt><dd>{{ $finance->reference_number ?? '—' }}</dd></div>
            <div><dt class="text-gray-400">Department</dt><dd>{{ $finance->department->name ?? '—' }}</dd></div>
            <div><dt class="text-gray-400">Recorded By</dt>
                <dd><a href="{{ route('members.show', $finance->recordedBy) }}" class="text-indigo-600 hover:underline">{{ $finance->recordedBy->full_name }}</a></dd>
            </div>
            @if($finance->description)
                <div><dt class="text-gray-400">Description</dt><dd>{{ $finance->description }}</dd></div>
            @endif
        </dl>
    </div>
</div>
@endsection
