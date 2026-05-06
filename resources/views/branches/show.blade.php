@extends('layouts.app')

@section('title', $branch->name)

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('branches.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
    <h1 class="text-2xl font-bold text-gray-900">{{ $branch->name }}</h1>
    <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-1 rounded-full">Alias: {{ $branch->alias }}</span>
    <div class="ml-auto flex gap-2">
        <a href="{{ route('branches.edit', $branch) }}" class="bg-yellow-50 hover:bg-yellow-100 text-yellow-700 text-sm font-medium px-3 py-1.5 rounded-lg border border-yellow-200">Edit</a>
        @if(auth()->user()->isAdmin())
            <form method="POST" action="{{ route('branches.destroy', $branch) }}" onsubmit="return confirm('Delete this branch?')">
                @csrf @method('DELETE')
                <button class="bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium px-3 py-1.5 rounded-lg border border-red-200">Delete</button>
            </form>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Branch info --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-3">
        <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Branch Info</h2>
        @if($branch->city)<p class="text-sm"><span class="text-gray-500">City:</span> {{ $branch->city }}</p>@endif
        @if($branch->regionRef || $branch->region)<p class="text-sm"><span class="text-gray-500">Region:</span> {{ $branch->regionRef?->name ?? $branch->region }}</p>@endif
        @if($branch->divisionRef || $branch->division)<p class="text-sm"><span class="text-gray-500">Division:</span> {{ $branch->divisionRef?->name ?? $branch->division }}</p>@endif
        @if($branch->subdivisionRef || $branch->subdivision)<p class="text-sm"><span class="text-gray-500">Subdivision:</span> {{ $branch->subdivisionRef?->name ?? $branch->subdivision }}</p>@endif
        @if($branch->address)<p class="text-sm"><span class="text-gray-500">Address:</span> {{ $branch->address }}</p>@endif
        @if($branch->display_phone)<p class="text-sm"><span class="text-gray-500">Phone:</span> {{ $branch->display_phone }}</p>@endif
        @if($branch->email)<p class="text-sm"><span class="text-gray-500">Email:</span> {{ $branch->email }}</p>@endif
        @if($branch->pastor)<p class="text-sm"><span class="text-gray-500">Pastor:</span> <a href="{{ route('members.show', $branch->pastor) }}" class="text-indigo-600 hover:underline">{{ $branch->pastor->full_name }}</a></p>@endif
        @if($branch->pastor?->display_phone)<p class="text-sm"><span class="text-gray-500">Pastor Contact:</span> {{ $branch->pastor->display_phone }}</p>@endif
        @if($branch->pastor?->email)<p class="text-sm"><span class="text-gray-500">Pastor Email:</span> {{ $branch->pastor->email }}</p>@endif
        @if($branch->parentBranch)<p class="text-sm"><span class="text-gray-500">Parent:</span> <a href="{{ route('branches.show', $branch->parentBranch) }}" class="text-indigo-600 hover:underline">{{ $branch->parentBranch->name }}</a></p>@endif
        @if($branch->description)<p class="text-sm text-gray-600 mt-2">{{ $branch->description }}</p>@endif

        @if($branch->subBranches->count())
            <div class="pt-3 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-500 mb-1">SUB-BRANCHES</p>
                @foreach($branch->subBranches as $sub)
                    <a href="{{ route('branches.show', $sub) }}" class="block text-sm text-indigo-600 hover:underline">{{ $sub->name }}</a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Members --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">Members ({{ $branch->members->count() }})</h2>
        @forelse($branch->members->take(10) as $member)
            <a href="{{ route('members.show', $member) }}" class="flex items-center gap-2 py-1.5 hover:bg-gray-50 rounded px-1 -mx-1">
                <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">{{ strtoupper(substr($member->first_name, 0, 1)) }}</div>
                <span class="text-sm text-gray-800">{{ $member->full_name }}</span>
            </a>
        @empty
            <p class="text-sm text-gray-400">No members yet.</p>
        @endforelse
        @if($branch->members->count() > 10)
            <p class="text-xs text-gray-500 mt-2">+{{ $branch->members->count() - 10 }} more</p>
        @endif
    </div>

    {{-- Recent events --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">Recent Events</h2>
        @forelse($branch->events as $event)
            <a href="{{ route('events.show', $event) }}" class="block py-1.5 border-b border-gray-50 last:border-0">
                <p class="text-sm font-medium text-gray-800">{{ $event->title }}</p>
                <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</p>
            </a>
        @empty
            <p class="text-sm text-gray-400">No events yet.</p>
        @endforelse
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">Branch Leadership</h2>
        @forelse($branchLeadershipMembers as $leader)
            <div class="py-2 border-b border-gray-50 last:border-0">
                <p class="text-sm font-medium text-gray-800">{{ $leader->full_name }}</p>
                <p class="text-xs text-gray-500">{{ $leader->leadershipRoles->pluck('name')->implode(', ') }}</p>
                <p class="text-xs text-gray-400">{{ $leader->display_phone ?? 'No contact' }}</p>
            </div>
        @empty
            <p class="text-sm text-gray-400">No branch leadership role assignments yet.</p>
        @endforelse
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">Departments & Leaders</h2>
        @forelse($departments as $department)
            <div class="py-2 border-b border-gray-50 last:border-0">
                <p class="text-sm font-medium text-gray-800">{{ $department->name }}</p>
                @php($heads = $department->members->where('pivot.role', 'head'))
                <p class="text-xs text-gray-500">Leader(s): {{ $heads->pluck('full_name')->implode(', ') ?: 'Not assigned' }}</p>
                <p class="text-xs text-gray-400">Members: {{ $department->members->count() }}</p>
            </div>
        @empty
            <p class="text-sm text-gray-400">No departments mapped to this branch yet.</p>
        @endforelse
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">Department Members</h2>
        @forelse($departments as $department)
            <div class="mb-4 last:mb-0">
                <p class="text-sm font-semibold text-gray-700 mb-1">{{ $department->name }}</p>
                @forelse($department->members as $member)
                    <p class="text-xs text-gray-600">{{ $member->full_name }} <span class="text-gray-400">({{ $member->pivot->role }})</span></p>
                @empty
                    <p class="text-xs text-gray-400">No members in this branch for this department.</p>
                @endforelse
            </div>
        @empty
            <p class="text-sm text-gray-400">No department memberships found.</p>
        @endforelse
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">Ministries & Members</h2>
        @forelse($ministries as $ministry)
            <div class="mb-4 last:mb-0">
                <p class="text-sm font-semibold text-gray-700 mb-1">{{ $ministry->name }}</p>
                @php($leaders = $ministry->members->where('pivot.role', 'leader'))
                <p class="text-xs text-gray-500 mb-1">Leader(s): {{ $leaders->pluck('full_name')->implode(', ') ?: 'Not assigned' }}</p>
                @forelse($ministry->members as $member)
                    <p class="text-xs text-gray-600">{{ $member->full_name }} <span class="text-gray-400">({{ $member->pivot->role }})</span></p>
                @empty
                    <p class="text-xs text-gray-400">No ministry members in this branch.</p>
                @endforelse
            </div>
        @empty
            <p class="text-sm text-gray-400">No ministry memberships found.</p>
        @endforelse
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mt-6 overflow-x-auto">
    <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">All Branch Members Info</h2>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr>
                <th class="px-3 py-2 text-left">Name</th>
                <th class="px-3 py-2 text-left">Contact</th>
                <th class="px-3 py-2 text-left">Status</th>
                <th class="px-3 py-2 text-left">Departments</th>
                <th class="px-3 py-2 text-left">Ministries</th>
                <th class="px-3 py-2 text-left">Leadership</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($branch->members->sortBy('full_name') as $member)
                <tr>
                    <td class="px-3 py-2">
                        <a href="{{ route('members.show', $member) }}" class="text-indigo-600 hover:underline">{{ $member->full_name }}</a>
                    </td>
                    <td class="px-3 py-2 text-gray-600">
                        <div>{{ $member->display_phone ?? '—' }}</div>
                        <div class="text-xs text-gray-400">{{ $member->email ?? 'No email' }}</div>
                    </td>
                    <td class="px-3 py-2 capitalize text-gray-600">{{ $member->status }}</td>
                    <td class="px-3 py-2 text-gray-600 text-xs">{{ $member->departments->pluck('name')->implode(', ') ?: '—' }}</td>
                    <td class="px-3 py-2 text-gray-600 text-xs">{{ $member->ministries->pluck('name')->implode(', ') ?: '—' }}</td>
                    <td class="px-3 py-2 text-gray-600 text-xs">{{ $member->leadershipRoles->pluck('name')->implode(', ') ?: '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-3 py-6 text-center text-gray-400">No members in this branch yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
