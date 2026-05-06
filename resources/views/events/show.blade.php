@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('events.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
    <h1 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h1>
    <div class="ml-auto flex gap-2">
        <a href="{{ route('events.edit', $event) }}" class="bg-yellow-50 border border-yellow-200 text-yellow-700 text-sm px-3 py-1.5 rounded-lg">Edit</a>
        <form method="POST" action="{{ route('events.destroy', $event) }}" onsubmit="return confirm('Delete this event?')">
            @csrf @method('DELETE')
            <button class="bg-red-50 border border-red-200 text-red-700 text-sm px-3 py-1.5 rounded-lg">Delete</button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Event details --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-2">
        <h2 class="font-semibold text-gray-600 text-xs uppercase tracking-wide mb-3">Event Details</h2>
        <p class="text-sm"><span class="text-gray-500">Type:</span> <span class="capitalize">{{ $event->type }}</span></p>
        <p class="text-sm"><span class="text-gray-500">Date:</span> {{ \Carbon\Carbon::parse($event->date)->format('D, M d Y') }}</p>
        @if($event->time)<p class="text-sm"><span class="text-gray-500">Time:</span> {{ \Carbon\Carbon::parse($event->time)->format('g:i A') }}</p>@endif
        <p class="text-sm"><span class="text-gray-500">Branch:</span> {{ $event->branch?->name ?? 'All branches' }}</p>
        <p class="text-sm"><span class="text-gray-500">Created by:</span> {{ $event->createdBy?->full_name ?? '—' }}</p>
        @if($event->description)<p class="text-sm text-gray-600 mt-2">{{ $event->description }}</p>@endif

        {{-- Attendance summary --}}
        @php
            $present = $event->attendances->where('status','present')->count();
            $absent  = $event->attendances->where('status','absent')->count();
            $excused = $event->attendances->where('status','excused')->count();
        @endphp
        <div class="pt-3 mt-3 border-t border-gray-100 grid grid-cols-3 text-center gap-2">
            <div><p class="text-lg font-bold text-green-600">{{ $present }}</p><p class="text-xs text-gray-500">Present</p></div>
            <div><p class="text-lg font-bold text-red-500">{{ $absent }}</p><p class="text-xs text-gray-500">Absent</p></div>
            <div><p class="text-lg font-bold text-yellow-500">{{ $excused }}</p><p class="text-xs text-gray-500">Excused</p></div>
        </div>
    </div>

    {{-- Mark Attendance --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-4">Mark Attendance</h2>
        <form method="POST" action="{{ route('events.attendance.save', $event) }}">
            @csrf
            <div class="overflow-y-auto max-h-[480px]">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-3 py-2 text-left text-gray-600 font-medium">Member</th>
                            <th class="px-3 py-2 text-left text-gray-600 font-medium">Status</th>
                            <th class="px-3 py-2 text-left text-gray-600 font-medium">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($members as $i => $member)
                            @php
                                $existing = $event->attendances->firstWhere('member_id', $member->id);
                                $status   = $existing?->status ?? 'present';
                                $notes    = $existing?->notes ?? '';
                            @endphp
                            <input type="hidden" name="attendances[{{ $i }}][member_id]" value="{{ $member->id }}">
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">{{ $member->full_name }}</td>
                                <td class="px-3 py-2">
                                    <select name="attendances[{{ $i }}][status]" class="border border-gray-200 rounded px-2 py-1 text-xs">
                                        <option value="present" {{ $status === 'present' ? 'selected' : '' }}>Present</option>
                                        <option value="absent" {{ $status === 'absent' ? 'selected' : '' }}>Absent</option>
                                        <option value="excused" {{ $status === 'excused' ? 'selected' : '' }}>Excused</option>
                                    </select>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" name="attendances[{{ $i }}][notes]" value="{{ $notes }}" class="border border-gray-200 rounded px-2 py-1 text-xs w-full">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg">Save Attendance</button>
            </div>
        </form>
    </div>
</div>
@endsection
