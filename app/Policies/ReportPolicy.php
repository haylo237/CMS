<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Report $report): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['super_admin', 'admin', 'pastor', 'hod', 'ministry_leader', 'finance_officer']);
    }

    public function update(User $user, Report $report): bool
    {
        if ($report->status !== 'draft') {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $report->submitted_by === $user->member_id;
    }

    public function submit(User $user, Report $report): bool
    {
        if ($report->status !== 'draft') {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($report->report_type === 'department' && $user->isHOD()) {
            // Ensure HOD is head of that department
            return $user->member->departments()
                        ->where('departments.id', $report->department_id)
                        ->wherePivot('role', 'head')
                        ->exists();
        }

        if ($report->report_type === 'ministry' && $user->isMinistryLeader()) {
            return $user->member->ministries()
                        ->where('ministries.id', $report->ministry_id)
                        ->wherePivot('role', 'leader')
                        ->exists();
        }

        if ($report->report_type === 'finance' && $user->isFinanceOfficer()) {
            return $report->submitted_by === $user->member_id;
        }

        return false;
    }

    public function review(User $user, Report $report): bool
    {
        if ($report->status !== 'submitted') {
            return false;
        }

        return $user->hasRole(['super_admin', 'admin', 'pastor']);
    }

    public function delete(User $user, Report $report): bool
    {
        return $user->isAdmin();
    }
}
