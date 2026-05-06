@extends('layouts.app')
@section('page-title', 'Finance')

@section('content')
@php($currency = \App\Models\Setting::currencySymbol())
{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
    <div class="bg-white rounded-xl border shadow-sm p-5">
        <p class="text-sm text-gray-500 mb-1">Total Income</p>
        <p class="text-2xl font-bold text-green-600">{{ $currency }}{{ number_format($totalIncome, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl border shadow-sm p-5">
        <p class="text-sm text-gray-500 mb-1">Total Expenses</p>
        <p class="text-2xl font-bold text-red-500">{{ $currency }}{{ number_format($totalExpense, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl border shadow-sm p-5">
        <p class="text-sm text-gray-500 mb-1">Net Balance</p>
        <p class="text-2xl font-bold {{ $balance >= 0 ? 'text-green-600' : 'text-red-500' }}">{{ $currency }}{{ number_format($balance, 2) }}</p>
    </div>
</div>

<div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-semibold">Transactions</h2>
    @can('manage-finance')
        <a href="{{ route('finance.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">+ Add Transaction</a>
    @endcan
</div>

{{-- Filters --}}
<form method="GET" class="bg-white rounded-xl border shadow-sm p-4 mb-6 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Type</label>
        <select name="type" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">All</option>
            <option value="income" @selected(request('type') === 'income')>Income</option>
            <option value="expense" @selected(request('type') === 'expense')>Expense</option>
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Department</label>
        <select name="department_id" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <option value="">All</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
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
    <a href="{{ route('finance.index') }}" class="text-sm text-gray-500 hover:underline py-2">Clear</a>
</form>

<div class="bg-white rounded-xl border shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-5 py-3 text-left">Date</th>
                <th class="px-5 py-3 text-left">Category</th>
                <th class="px-5 py-3 text-left">Department</th>
                <th class="px-5 py-3 text-left">Recorded By</th>
                <th class="px-5 py-3 text-right">Amount</th>
                <th class="px-5 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($transactions as $txn)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 text-gray-500">{{ $txn->transaction_date->format('d M Y') }}</td>
                    <td class="px-5 py-3 font-medium">{{ $txn->category }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $txn->department->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $txn->recordedBy->full_name }}</td>
                    <td class="px-5 py-3 text-right font-semibold {{ $txn->type === 'income' ? 'text-green-600' : 'text-red-500' }}">
                        {{ $txn->type === 'income' ? '+' : '-' }}{{ $currency }}{{ number_format($txn->amount, 2) }}
                    </td>
                    <td class="px-5 py-3 flex items-center gap-2">
                        <a href="{{ route('finance.show', $txn) }}" class="text-gray-400 hover:text-indigo-600 transition"><i class="fa-solid fa-eye"></i></a>
                        @can('manage-finance')
                            <a href="{{ route('finance.edit', $txn) }}" class="text-gray-400 hover:text-amber-500 transition"><i class="fa-solid fa-pen-to-square"></i></a>
                            <form method="POST" action="{{ route('finance.destroy', $txn) }}" onsubmit="return confirm('Delete transaction?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-400 hover:text-red-500 transition"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No transactions found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-4 border-t">{{ $transactions->links() }}</div>
</div>
@endsection
