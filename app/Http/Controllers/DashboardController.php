<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Event;
use App\Models\FinanceTransaction;
use App\Models\Member;
use App\Models\Ministry;
use App\Models\Report;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $branchId = $user && $user->isPastor() ? $user->pastoredBranchId() : null;
        $isBranchPastor = $user && $user->isPastor() && $branchId;

        $memberQuery = Member::query();
        $reportQuery = Report::query();
        $financeQuery = FinanceTransaction::query();
        $eventQuery = Event::query();
        $announcementQuery = Announcement::query();
        $branchQuery = Branch::query();

        if ($isBranchPastor) {
            $memberQuery->where('branch_id', $branchId);
            $reportQuery->whereHas('submittedBy', fn($q) => $q->where('branch_id', $branchId));
            $financeQuery->whereHas('recordedBy', fn($q) => $q->where('branch_id', $branchId));
            $eventQuery->where(function ($q) use ($branchId) {
                $q->whereNull('branch_id')->orWhere('branch_id', $branchId);
            });
            $announcementQuery->where(function ($q) use ($branchId) {
                $q->where('audience', 'all')->orWhere('branch_id', $branchId);
            });
            $branchQuery->where('id', $branchId);
        }

        // Core counts
        $stats = [
            'total_members'      => (clone $memberQuery)->count(),
            'active_members'     => (clone $memberQuery)->where('status', 'active')->count(),
            'total_branches'     => (clone $branchQuery)->count(),
            'total_departments'  => $isBranchPastor ? Department::whereHas('members', fn($q) => $q->where('members.branch_id', $branchId))->count() : Department::count(),
            'total_ministries'   => $isBranchPastor ? Ministry::whereHas('members', fn($q) => $q->where('members.branch_id', $branchId))->count() : Ministry::count(),
            'reports_submitted'  => (clone $reportQuery)->where('status', 'submitted')->count(),
            'reports_reviewed'   => (clone $reportQuery)->where('status', 'reviewed')->count(),
            'total_income'       => (clone $financeQuery)->income()->sum('amount'),
            'total_expense'      => (clone $financeQuery)->expense()->sum('amount'),
            'upcoming_events'    => (clone $eventQuery)->upcoming()->limit(5)->get(),
            'recent_reports'     => (clone $reportQuery)->with(['submittedBy', 'department', 'ministry'])->latest()->limit(5)->get(),
            'recent_transactions'=> (clone $financeQuery)->with('recordedBy')->latest()->limit(5)->get(),
            'active_announcements'=> (clone $announcementQuery)->active()->with('publishedBy')->latest()->limit(5)->get(),
        ];

        // Member growth — last 6 months
        $memberGrowth = $memberQuery->select(
                DB::raw("TO_CHAR(created_at, 'Mon YYYY') as month"),
                DB::raw("DATE_TRUNC('month', created_at) as month_start"),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('month_start', 'month')
            ->orderBy('month_start')
            ->get();

        // Attendance trend — last 6 events
        $attendanceTrend = $eventQuery->withCount(['attendances as present_count' => fn($q) => $q->where('status', 'present')])
            ->past()
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        // Finance trend — last 6 months income vs expense
        $financeTrend = $financeQuery->select(
                DB::raw("TO_CHAR(transaction_date, 'Mon YYYY') as month"),
                DB::raw("DATE_TRUNC('month', transaction_date) as month_start"),
                'type',
                DB::raw('SUM(amount) as total')
            )
            ->where('transaction_date', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('month_start', 'month', 'type')
            ->orderBy('month_start')
            ->get()
            ->groupBy('month');

        // Branch member distribution
        $branchStats = $branchQuery->withCount('members')->get();

        return view('dashboard', compact('stats', 'memberGrowth', 'attendanceTrend', 'financeTrend', 'branchStats'));
    }
}
