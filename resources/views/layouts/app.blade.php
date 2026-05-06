<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Church CMS') — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="h-full font-sans antialiased text-gray-800">

<div class="flex h-full">
    {{-- Sidebar --}}
    <aside class="w-64 bg-indigo-900 text-white flex flex-col min-h-screen fixed inset-y-0 left-0 z-20">
        <div class="flex items-center gap-3 px-6 py-5 border-b border-indigo-700">
            <i class="fa-solid fa-church text-indigo-300 text-2xl"></i>
            <span class="text-lg font-bold tracking-wide">Church CMS</span>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            @php
                $navItems = [
                    ['route' => 'dashboard',              'icon' => 'fa-gauge',             'label' => 'Dashboard'],
                    ['route' => 'members.index',           'icon' => 'fa-users',             'label' => 'Members'],
                    ['route' => 'branches.index',          'icon' => 'fa-code-branch',       'label' => 'Branches'],
                    ['route' => 'departments.index',       'icon' => 'fa-building',          'label' => 'Departments'],
                    ['route' => 'ministries.index',        'icon' => 'fa-hands-praying',     'label' => 'Ministries'],
                    ['route' => 'leadership.index',        'icon' => 'fa-crown',             'label' => 'Leadership'],
                    ['route' => 'events.index',            'icon' => 'fa-calendar-days',     'label' => 'Events'],
                    ['route' => 'announcements.index',     'icon' => 'fa-bullhorn',          'label' => 'Announcements'],
                    ['route' => 'reports.index',           'icon' => 'fa-chart-bar',         'label' => 'Reports'],
                    ['route' => 'finance.index',           'icon' => 'fa-coins',             'label' => 'Finance'],
                ];
            @endphp

            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs(rtrim($item['route'], '.index') . '*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                    <i class="fa-solid {{ $item['icon'] }} w-5 text-center"></i>
                    {{ $item['label'] }}
                </a>
            @endforeach

            @can('manage-users')
                <a href="{{ route('users.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('users*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                    <i class="fa-solid fa-user-gear w-5 text-center"></i>
                    Users
                </a>
            @endcan

            @can('manage-settings')
                <a href="{{ route('settings.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('settings*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                    <i class="fa-solid fa-gear w-5 text-center"></i>
                    Settings
                </a>
            @endcan

            @auth
                @php
                    $msgMemberId    = auth()->user()->member_id;
                    $unreadMessages = $msgMemberId ? \App\Models\Message::where('recipient_id', $msgMemberId)->whereNull('read_at')->count() : 0;
                @endphp
                <a href="{{ route('messages.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs('messages*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                    <i class="fa-solid fa-envelope w-5 text-center"></i>
                    Messages
                    @if($unreadMessages > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">{{ $unreadMessages }}</span>
                    @endif
                </a>
            @endauth
        </nav>

        <div class="px-4 py-4 border-t border-indigo-700">
            <div class="flex items-center gap-3 mb-3 px-3">
                <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-xs font-bold">
                    {{ strtoupper(substr(auth()->user()->member->first_name ?? 'A', 0, 1)) }}
                </div>
                <div class="text-xs">
                    <p class="font-medium text-white">{{ auth()->user()->member->full_name ?? 'Admin' }}</p>
                    <p class="text-indigo-300 capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full flex items-center gap-2 text-indigo-300 hover:text-white text-sm px-3 py-2 rounded transition">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 ml-64 min-h-screen">
        {{-- Top Bar --}}
        <header class="bg-white border-b px-8 py-4 flex items-center justify-between sticky top-0 z-10 shadow-sm">
            <h1 class="text-xl font-semibold text-gray-700">@yield('page-title', 'Dashboard')</h1>
            <div class="flex items-center gap-4 text-sm text-gray-500">
                <span>{{ now()->format('D, d M Y') }}</span>
            </div>
        </header>

        <div class="p-8">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <p class="font-medium mb-1">Please fix the following errors:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

</body>
</html>
