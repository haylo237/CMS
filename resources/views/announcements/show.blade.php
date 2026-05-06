@extends('layouts.app')

@section('title', $announcement->title)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('announcements.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $announcement->title }}</h1>
        <div class="ml-auto flex gap-2">
            @can('send-whatsapp')
            <button type="button" onclick="document.getElementById('waModal').classList.remove('hidden')"
                class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1.5 rounded-lg flex items-center gap-1.5">
                <i class="fa-brands fa-whatsapp"></i> Send to WhatsApp
            </button>
            @endcan
            <a href="{{ route('announcements.edit', $announcement) }}" class="bg-yellow-50 border border-yellow-200 text-yellow-700 text-sm px-3 py-1.5 rounded-lg">Edit</a>
            <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" onsubmit="return confirm('Delete?')">
                @csrf @method('DELETE')
                <button class="bg-red-50 border border-red-200 text-red-700 text-sm px-3 py-1.5 rounded-lg">Delete</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

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

    {{-- WhatsApp send history --}}
    @can('send-whatsapp')
    @if($sendLogs->count())
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
            <i class="fa-brands fa-whatsapp text-green-500"></i> WhatsApp Send History
        </h3>
        <table class="w-full text-sm text-gray-700">
            <thead class="text-xs text-gray-500 border-b border-gray-100">
                <tr>
                    <th class="text-left pb-2">Date</th>
                    <th class="text-left pb-2">Audience</th>
                    <th class="text-right pb-2">Recipients</th>
                    <th class="text-right pb-2">Sent</th>
                    <th class="text-right pb-2">Failed</th>
                    <th class="text-left pb-2">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($sendLogs as $log)
                <tr>
                    <td class="py-2 text-xs text-gray-400">{{ $log->created_at->format('M d, Y g:i A') }}</td>
                    <td class="py-2">{{ ucfirst($log->audience_type) }}@if($log->audience_id) <span class="text-gray-400 text-xs">(#{{ $log->audience_id }})</span>@endif</td>
                    <td class="py-2 text-right">{{ $log->total_recipients }}</td>
                    <td class="py-2 text-right text-green-600">{{ $log->sent_count }}</td>
                    <td class="py-2 text-right text-red-500">{{ $log->failed_count }}</td>
                    <td class="py-2">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            {{ $log->status === 'completed' ? 'bg-green-50 text-green-700' : '' }}
                            {{ $log->status === 'sending'   ? 'bg-blue-50 text-blue-700' : '' }}
                            {{ $log->status === 'pending'   ? 'bg-yellow-50 text-yellow-700' : '' }}
                            {{ $log->status === 'failed'    ? 'bg-red-50 text-red-700' : '' }}
                        ">{{ ucfirst($log->status) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    @endcan
</div>

{{-- WhatsApp Broadcast Modal --}}
@can('send-whatsapp')
<div id="waModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                <i class="fa-brands fa-whatsapp text-green-500 text-lg"></i> Send to WhatsApp
            </h3>
            <button onclick="document.getElementById('waModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('announcements.whatsapp', $announcement) }}">
            @csrf
            <div class="px-6 py-5 space-y-5">
                <p class="text-sm text-gray-500">
                    This will queue a WhatsApp message to all selected active members who have a phone number recorded.
                </p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Send to</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="audience_type" value="all" checked onchange="toggleWaFields(this.value)" class="text-green-600">
                            <span class="text-sm text-gray-700">All active members</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="audience_type" value="branch" onchange="toggleWaFields(this.value)" class="text-green-600">
                            <span class="text-sm text-gray-700">Specific Branch</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="audience_type" value="department" onchange="toggleWaFields(this.value)" class="text-green-600">
                            <span class="text-sm text-gray-700">Specific Department</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="audience_type" value="ministry" onchange="toggleWaFields(this.value)" class="text-green-600">
                            <span class="text-sm text-gray-700">Specific Ministry</span>
                        </label>
                    </div>
                </div>
                <div id="waFieldBranch" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                    <select name="audience_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="waFieldDepartment" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select name="audience_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="waFieldMinistry" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ministry</label>
                    <select name="audience_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @foreach($ministries as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="px-6 pb-5 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('waModal').classList.add('hidden')"
                    class="text-sm px-4 py-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-5 py-2 rounded-lg flex items-center gap-1.5">
                    <i class="fa-brands fa-whatsapp"></i> Broadcast Now
                </button>
            </div>
        </form>
    </div>
</div>
<script>
function toggleWaFields(type) {
    ['Branch','Department','Ministry'].forEach(t => {
        const el = document.getElementById('waField' + t);
        if (el) el.classList.toggle('hidden', t.toLowerCase() !== type);
    });
}
</script>
@endcan
@endsection
