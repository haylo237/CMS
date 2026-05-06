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
        // Core counts
        $stats = [
            'total_members'      => Member::count(),
            'active_members'     => Member::where('status', 'active')->count(),
            'total_branches'     => Branch::count(),
            'total_departments'  => Department::count(),
            'total_ministries'   => Ministry::count(),
            'reports_submitted'  => Report::where('status', 'submitted')->count(),
            'reports_reviewed'   => Report::where('status', 'reviewed')->count(),
            'total_income'       => FinanceTransaction::income()->sum('amount'),
            'total_expense'      => FinanceTransaction::expense()->sum('amount'),
            'upcoming_events'    => Event::upcoming()->limit(5)->get(),
            'recent_reports'     => Report::with(['submittedBy', 'department', 'ministry'])->latest()->limit(5)->get(),
            'recent_transactions'=> FinanceTransaction::with('recordedBy')->latest()->limit(5)->get(),
            'active_announcements'=> Announcement::active()->with('publishedBy')->latest()->limit(5)->get(),
        ];

        // Member growth — last 6 months
        $memberGrowth = Member::select(
                DB::raw("TO_CHAR(created_at, 'Mon YYYY') as month"),
                DB::raw("DATE_TRUNC('month', created_at) as month_start"),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('month_start', 'month')
            ->orderBy('month_start')
            ->get();

        // Attendance trend — last 6 events
        $attendanceTrend = Event::withCount(['attendances as present_count' => fn($q) => $q->where('status', 'present')])
            ->past()
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        // Finance trend — last 6 months income vs expense
        $financeTrend = FinanceTransaction::select(
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
        $branchStats = Branch::withCount('members')->get();

        return view('dashboard', compact('stats', 'memberGrowth', 'attendanceTrend', 'financeTrend', 'branchStats'));
    }
}
