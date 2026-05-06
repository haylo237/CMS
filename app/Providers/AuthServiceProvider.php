<?php

namespace App\Providers;

use App\Models\LeadershipRole;
use App\Models\Report;
use App\Policies\ReportPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Report::class => ReportPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Admin-level gates
        Gate::define('manage-users', fn($user) => $user->isAdmin());
        Gate::define('manage-settings', fn($user) => $user->isAdmin());
        Gate::define('view-finance', fn($user) => $user->hasRole(['super_admin', 'admin', 'pastor', 'finance_officer']));
        Gate::define('manage-finance', fn($user) => $user->hasRole(['super_admin', 'admin', 'finance_officer']));
        Gate::define('manage-members', fn($user) => $user->hasRole(['super_admin', 'admin']));
        Gate::define('manage-departments', fn($user) => $user->hasRole(['super_admin', 'admin']));
        Gate::define('manage-ministries', fn($user) => $user->hasRole(['super_admin', 'admin']));
        Gate::define('manage-leadership', fn($user) => $user->hasRole(['super_admin', 'admin']));

        // General Overseer gate: super_admin OR member with a leadership role titled "General Overseer"
        Gate::define('send-whatsapp', function ($user) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            if (!$user->member_id) {
                return false;
            }
            return \App\Models\Member::where('id', $user->member_id)
                ->whereHas('leadershipRoles', fn($q) => $q->where('title', 'General Overseer'))
                ->exists();
        });

        // Super admin bypass
        Gate::before(fn($user, $ability) => $user->isSuperAdmin() ? true : null);
    }
}
