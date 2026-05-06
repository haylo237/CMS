<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Ministry;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = Report::with(['submittedBy', 'department', 'ministry']);
        $branchId = auth()->user()?->isPastor() ? auth()->user()->pastoredBranchId() : null;

        if ($branchId) {
            $query->whereHas('submittedBy', fn($q) => $q->where('branch_id', $branchId));
        }

        if ($request->filled('type')) {
            $query->where('report_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('ministry_id')) {
            $query->where('ministry_id', $request->ministry_id);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('reporting_period_start', [$request->date_from, $request->date_to]);
        }

        $reports     = $query->latest()->paginate(20)->withQueryString();
        $departments = Department::orderBy('name')->get();
        $ministries  = Ministry::orderBy('name')->get();

        return view('reports.index', compact('reports', 'departments', 'ministries'));
    }

    public function create(): View
    {
        $departments = Department::orderBy('name')->get();
        $ministries  = Ministry::orderBy('name')->get();

        return view('reports.create', compact('departments', 'ministries'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'                  => 'required|string|max:255',
            'description'            => 'nullable|string',
            'report_type'            => 'required|in:department,ministry,finance',
            'department_id'          => 'required_if:report_type,department|nullable|exists:departments,id',
            'ministry_id'            => 'required_if:report_type,ministry|nullable|exists:ministries,id',
            'reporting_period_start' => 'required|date',
            'reporting_period_end'   => 'required|date|after_or_equal:reporting_period_start',
            'metadata'               => 'nullable|array',
        ]);

        $validated['submitted_by'] = auth()->user()->member_id;
        $validated['status']       = 'draft';

        $report = Report::create($validated);

        return redirect()->route('reports.show', $report)
                         ->with('success', 'Report created successfully.');
    }

    public function show(Report $report): View
    {
        if (auth()->user()?->isPastor() && $report->submittedBy?->branch_id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        $report->load(['submittedBy', 'reviewedBy', 'department', 'ministry']);

        return view('reports.show', compact('report'));
    }

    public function edit(Report $report): View
    {
        if (auth()->user()?->isPastor() && $report->submittedBy?->branch_id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        Gate::authorize('update', $report);

        $departments = Department::orderBy('name')->get();
        $ministries  = Ministry::orderBy('name')->get();

        return view('reports.edit', compact('report', 'departments', 'ministries'));
    }

    public function update(Request $request, Report $report): RedirectResponse
    {
        if (auth()->user()?->isPastor() && $report->submittedBy?->branch_id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        Gate::authorize('update', $report);

        $validated = $request->validate([
            'title'                  => 'required|string|max:255',
            'description'            => 'nullable|string',
            'report_type'            => 'required|in:department,ministry,finance',
            'department_id'          => 'required_if:report_type,department|nullable|exists:departments,id',
            'ministry_id'            => 'required_if:report_type,ministry|nullable|exists:ministries,id',
            'reporting_period_start' => 'required|date',
            'reporting_period_end'   => 'required|date|after_or_equal:reporting_period_start',
            'metadata'               => 'nullable|array',
        ]);

        $report->update($validated);

        return redirect()->route('reports.show', $report)
                         ->with('success', 'Report updated.');
    }

    public function submit(Report $report): RedirectResponse
    {
        if (auth()->user()?->isPastor() && $report->submittedBy?->branch_id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        Gate::authorize('submit', $report);

        $report->update(['status' => 'submitted']);

        return redirect()->route('reports.show', $report)
                         ->with('success', 'Report submitted for review.');
    }

    public function review(Request $request, Report $report): RedirectResponse
    {
        if (auth()->user()?->isPastor() && $report->submittedBy?->branch_id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        Gate::authorize('review', $report);

        $request->validate([
            'review_notes' => 'nullable|string',
        ]);

        $report->update([
            'status'       => 'reviewed',
            'reviewed_by'  => auth()->user()->member_id,
            'reviewed_at'  => now(),
            'review_notes' => $request->review_notes,
        ]);

        return redirect()->route('reports.show', $report)
                         ->with('success', 'Report reviewed.');
    }

    public function destroy(Report $report): RedirectResponse
    {
        if (auth()->user()?->isPastor() && $report->submittedBy?->branch_id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        Gate::authorize('delete', $report);

        $report->delete();

        return redirect()->route('reports.index')
                         ->with('success', 'Report deleted.');
    }
}
