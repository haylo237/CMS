@extends('layouts.app')

@section('title', $branch->name)

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('branches.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
    <h1 class="text-2xl font-bold text-gray-900">{{ $branch->name }}</h1>
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
@endsection
