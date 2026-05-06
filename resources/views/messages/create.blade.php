@extends('layouts.app')

@section('title', 'Compose Message')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('messages.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-gray-900">Compose Message</h1>
    </div>

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('messages.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">To *</label>
            <select name="recipient_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">— Select recipient —</option>
                @foreach($members as $m)
                    @if($m->id !== auth()->user()->member_id)
                        <option value="{{ $m->id }}" {{ old('recipient_id') == $m->id ? 'selected' : '' }}>{{ $m->full_name }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
            <input type="text" name="subject" value="{{ old('subject') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
            <textarea name="body" rows="6" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('body') }}</textarea>
        </div>
        <div class="flex gap-3 justify-end pt-2">
            <a href="{{ route('messages.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium flex items-center gap-2">
                <i class="fa-solid fa-paper-plane"></i> Send
            </button>
        </div>
    </form>
</div>
@endsection
