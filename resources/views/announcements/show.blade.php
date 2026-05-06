@extends('layouts.app')

@section('title', $announcement->title)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('announcements.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $announcement->title }}</h1>
        <div class="ml-auto flex gap-2">
            <a href="{{ route('announcements.edit', $announcement) }}" class="bg-yellow-50 border border-yellow-200 text-yellow-700 text-sm px-3 py-1.5 rounded-lg">Edit</a>
            <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" onsubmit="return confirm('Delete?')">
                @csrf @method('DELETE')
                <button class="bg-red-50 border border-red-200 text-red-700 text-sm px-3 py-1.5 rounded-lg">Delete</button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-wrap gap-2 mb-4">
            <span class="text-xs px-2 py-1 rounded-full font-medium
                {{ $announcement->audience === 'all' ? 'bg-blue-50 text-blue-700' : '' }}
                {{ $announcement->audience === 'branch' ? 'bg-green-50 text-green-700' : '' }}
                {{ $announcement->audience === 'department' ? 'bg-yellow-50 text-yellow-700' : '' }}
                {{ $announcement->audience === 'ministry' ? 'bg-purple-50 text-purple-700' : '' }}
            ">{{ ucfirst($announcement->audience) }}</span>
            @if($announcement->branch)<span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-full">{{ $announcement->branch->name }}</span>@endif
            @if($announcement->department)<span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-full">{{ $announcement->department->name }}</span>@endif
            @if($announcement->ministry)<span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-full">{{ $announcement->ministry->name }}</span>@endif
        </div>

        <div class="prose prose-sm max-w-none text-gray-700 mb-6 whitespace-pre-line">{{ $announcement->body }}</div>

        <div class="text-xs text-gray-400 border-t border-gray-100 pt-4 space-y-1">
            <p>Published by: {{ $announcement->publishedBy?->full_name ?? '—' }}</p>
            @if($announcement->published_at)<p>Published at: {{ $announcement->published_at->format('M d, Y g:i A') }}</p>@endif
            @if($announcement->expires_at)<p>Expires at: {{ $announcement->expires_at->format('M d, Y g:i A') }}</p>@endif
        </div>
    </div>
</div>
@endsection
