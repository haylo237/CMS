<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\FinanceTransaction;
use App\Models\Member;
use App\Models\Ministry;
use App\Models\Report;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_members'      => Member::count(),
            'active_members'     => Member::where('status', 'active')->count(),
            'total_departments'  => Department::count(),
            'total_ministries'   => Ministry::count(),
            'recent_reports'     => Report::with(['submittedBy', 'department', 'ministry'])
                                          ->latest()
                                          ->limit(5)
                                          ->get(),
            'reports_submitted'  => Report::where('status', 'submitted')->count(),
            'reports_reviewed'   => Report::where('status', 'reviewed')->count(),
            'total_income'       => FinanceTransaction::income()->sum('amount'),
            'total_expense'      => FinanceTransaction::expense()->sum('amount'),
            'recent_transactions'=> FinanceTransaction::with('recordedBy')
                                                       ->latest()
                                                       ->limit(5)
                                                       ->get(),
        ];

        return view('dashboard', compact('stats'));
    }
}
