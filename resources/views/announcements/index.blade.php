@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Announcements</h1>
    <a href="{{ route('announcements.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        <i class="fa-solid fa-plus"></i> New Announcement
    </a>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

<div class="space-y-4">
    @forelse($announcements as $a)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <h2 class="font-semibold text-gray-900">{{ $a->title }}</h2>
                        <span class="text-xs px-2 py-0.5 rounded-full
                            {{ $a->audience === 'all' ? 'bg-blue-50 text-blue-700' : '' }}
                            {{ $a->audience === 'branch' ? 'bg-green-50 text-green-700' : '' }}
                            {{ $a->audience === 'department' ? 'bg-yellow-50 text-yellow-700' : '' }}
                            {{ $a->audience === 'ministry' ? 'bg-purple-50 text-purple-700' : '' }}
                        ">{{ ucfirst($a->audience) }}</span>
                        @if($a->expires_at && $a->expires_at->isPast())
                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Expired</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 line-clamp-2">{{ $a->body }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        Published by {{ $a->publishedBy?->full_name ?? '—' }}
                        @if($a->published_at) · {{ $a->published_at->format('M d, Y') }} @endif
                        @if($a->branch) · {{ $a->branch->name }} @endif
                        @if($a->department) · {{ $a->department->name }} @endif
                        @if($a->ministry) · {{ $a->ministry->name }} @endif
                    </p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <a href="{{ route('announcements.show', $a) }}" class="text-indigo-600 hover:underline text-sm">View</a>
                    <a href="{{ route('announcements.edit', $a) }}" class="text-yellow-600 hover:underline text-sm">Edit</a>
                    <form method="POST" action="{{ route('announcements.destroy', $a) }}" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:underline text-sm">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-16 text-gray-400">
            <i class="fa-solid fa-bullhorn text-4xl mb-3"></i>
            <p>No announcements yet. <a href="{{ route('announcements.create') }}" class="text-indigo-600 underline">Create one</a>.</p>
        </div>
    @endforelse
</div>

<div class="mt-6">{{ $announcements->links() }}</div>
@endsection
