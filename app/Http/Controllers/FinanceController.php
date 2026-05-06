<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\FinanceTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function index(Request $request): View
    {
        $query = FinanceTransaction::with(['recordedBy', 'department']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', 'ilike', '%' . $request->category . '%');
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('transaction_date', [$request->date_from, $request->date_to]);
        }

        $transactions = $query->orderByDesc('transaction_date')->paginate(20)->withQueryString();
        $departments  = Department::orderBy('name')->get();

        $totalIncome  = FinanceTransaction::income()->sum('amount');
        $totalExpense = FinanceTransaction::expense()->sum('amount');
        $balance      = $totalIncome - $totalExpense;

        // Monthly summary for current year
        $monthlySummary = FinanceTransaction::selectRaw(
            "DATE_TRUNC('month', transaction_date) AS month,
             SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) AS income,
             SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) AS expense"
        )
        ->whereYear('transaction_date', now()->year)
        ->groupByRaw("DATE_TRUNC('month', transaction_date)")
        ->orderByRaw("DATE_TRUNC('month', transaction_date)")
        ->get();

        return view('finance.index', compact(
            'transactions', 'departments',
            'totalIncome', 'totalExpense', 'balance', 'monthlySummary'
        ));
    }

    public function create(): View
    {
        $departments = Department::orderBy('name')->get();

        return view('finance.create', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount'           => 'required|numeric|min:0.01',
            'type'             => 'required|in:income,expense',
            'category'         => 'required|string|max:255',
            'description'      => 'nullable|string',
            'department_id'    => 'nullable|exists:departments,id',
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $validated['recorded_by'] = auth()->user()->member_id;

        FinanceTransaction::create($validated);

        return redirect()->route('finance.index')
                         ->with('success', 'Transaction recorded successfully.');
    }

    public function show(FinanceTransaction $finance): View
    {
        $finance->load(['recordedBy', 'department']);

        return view('finance.show', compact('finance'));
    }

    public function edit(FinanceTransaction $finance): View
    {
        $departments = Department::orderBy('name')->get();

        return view('finance.edit', compact('finance', 'departments'));
    }

    public function update(Request $request, FinanceTransaction $finance): RedirectResponse
    {
        $validated = $request->validate([
            'amount'           => 'required|numeric|min:0.01',
            'type'             => 'required|in:income,expense',
            'category'         => 'required|string|max:255',
            'description'      => 'nullable|string',
            'department_id'    => 'nullable|exists:departments,id',
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $finance->update($validated);

        return redirect()->route('finance.show', $finance)
                         ->with('success', 'Transaction updated.');
    }

    public function destroy(FinanceTransaction $finance): RedirectResponse
    {
        $finance->delete();

        return redirect()->route('finance.index')
                         ->with('success', 'Transaction deleted.');
    }
}
