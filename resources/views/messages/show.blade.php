@extends('layouts.app')

@section('title', $message->subject)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('messages.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 class="text-xl font-bold text-gray-900">{{ $message->subject }}</h1>
        <div class="ml-auto">
            <form method="POST" action="{{ route('messages.destroy', $message) }}" onsubmit="return confirm('Delete this message?')">
                @csrf @method('DELETE')
                <button class="bg-red-50 border border-red-200 text-red-700 text-sm px-3 py-1.5 rounded-lg">Delete</button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start gap-4 mb-6 pb-4 border-b border-gray-100">
            <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold shrink-0">
                {{ strtoupper(substr($message->sender?->first_name ?? '?', 0, 1)) }}
            </div>
            <div>
                <p class="font-medium text-gray-900">{{ $message->sender?->full_name ?? 'Unknown' }}</p>
                <p class="text-sm text-gray-500">To: {{ $message->recipient?->full_name ?? 'Unknown' }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $message->created_at->format('M d, Y g:i A') }}</p>
            </div>
            @if($message->read_at)
                <span class="ml-auto text-xs text-gray-400 flex items-center gap-1"><i class="fa-solid fa-check-double text-blue-400"></i> Read</span>
            @endif
        </div>

        <div class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $message->body }}</div>

        @if($message->recipient_id === auth()->user()->member_id)
            <div class="mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('messages.create') }}?to={{ $message->sender_id }}" class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:underline">
                    <i class="fa-solid fa-reply"></i> Reply
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
