@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Messages</h1>
    <a href="{{ route('messages.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        <i class="fa-solid fa-pen-to-square"></i> Compose
    </a>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Inbox --}}
    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-inbox text-indigo-500"></i> Inbox
            @if($unreadCount > 0)
                <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
            @endif
        </h2>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 divide-y divide-gray-50">
            @forelse($inbox as $msg)
                <a href="{{ route('messages.show', $msg) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 {{ !$msg->read_at ? 'bg-indigo-50' : '' }}">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                        {{ strtoupper(substr($msg->sender?->first_name ?? '?', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-medium text-gray-900 truncate {{ !$msg->read_at ? 'font-semibold' : '' }}">{{ $msg->sender?->full_name ?? 'Unknown' }}</p>
                            <span class="text-xs text-gray-400 shrink-0">{{ $msg->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-700 truncate">{{ $msg->subject }}</p>
                    </div>
                </a>
            @empty
                <p class="px-4 py-8 text-center text-sm text-gray-400">No messages in inbox.</p>
            @endforelse
        </div>
        <div class="mt-2">{{ $inbox->links() }}</div>
    </div>

    {{-- Sent --}}
    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-paper-plane text-indigo-500"></i> Sent
        </h2>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 divide-y divide-gray-50">
            @forelse($sent as $msg)
                <a href="{{ route('messages.show', $msg) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50">
                    <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                        {{ strtoupper(substr($msg->recipient?->first_name ?? '?', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-medium text-gray-900 truncate">To: {{ $msg->recipient?->full_name ?? 'Unknown' }}</p>
                            <span class="text-xs text-gray-400 shrink-0">{{ $msg->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-700 truncate">{{ $msg->subject }}</p>
                    </div>
                </a>
            @empty
                <p class="px-4 py-8 text-center text-sm text-gray-400">No sent messages.</p>
            @endforelse
        </div>
        <div class="mt-2">{{ $sent->links() }}</div>
    </div>
</div>
@endsection
