@extends('layouts.app')
@section('page-title', $member->full_name)

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('members.index') }}" class="text-gray-400 hover:text-gray-600 transition">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h2 class="text-lg font-semibold">{{ $member->full_name }}</h2>
    @can('manage-members')
        <a href="{{ route('members.edit', $member) }}"
           class="ml-auto text-sm bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 px-3 py-1.5 rounded-lg transition">
            Edit
        </a>
    @endcan
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Profile Info --}}
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <div class="mb-4 flex justify-center">
            @if($member->profile_photo_url)
                <img src="{{ $member->profile_photo_url }}" alt="{{ $member->full_name }}" class="w-24 h-24 rounded-full object-cover border-2 border-indigo-100">
            @else
                <div class="w-24 h-24 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center border-2 border-gray-200 text-2xl">
                    <i class="fa-solid fa-user"></i>
                </div>
            @endif
        </div>
        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Personal Info</h3>
        <dl class="space-y-3 text-sm">
            <div><dt class="text-gray-400">Full Name</dt><dd class="font-medium">{{ $member->full_name }}</dd></div>
            <div><dt class="text-gray-400">Email</dt><dd>{{ $member->email ?? '—' }}</dd></div>
            <div><dt class="text-gray-400">Phone</dt><dd>{{ $member->display_phone ?? '—' }}</dd></div>
            <div><dt class="text-gray-400">Gender</dt><dd class="capitalize">{{ $member->gender ?? '—' }}</dd></div>
            <div><dt class="text-gray-400">Date of Birth</dt><dd>{{ $member->date_of_birth?->format('d M Y') ?? '—' }}</dd></div>
            <div><dt class="text-gray-400">Status</dt>
                <dd><span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $member->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ ucfirst($member->status) }}</span></dd>
            </div>
            <div><dt class="text-gray-400">Address</dt><dd>{{ $member->address ?? '—' }}</dd></div>
        </dl>
    </div>

    {{-- Assignments --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Departments --}}
        <div class="bg-white rounded-xl border shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Departments</h3>
            @if($member->departments->isNotEmpty())
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($member->departments as $dept)
                        <div class="flex items-center gap-1 bg-indigo-50 text-indigo-700 text-sm px-3 py-1 rounded-full border border-indigo-100">
                            {{ $dept->name }} <span class="text-indigo-400 text-xs">({{ $dept->pivot->role }})</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 mb-4">Not assigned to any department.</p>
            @endif

            <p class="text-xs text-gray-500">Department leaders and membership are managed from each department's edit page.</p>
        </div>

        {{-- Ministries --}}
        <div class="bg-white rounded-xl border shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Ministries</h3>
            @if($member->ministries->isNotEmpty())
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($member->ministries as $min)
                        <div class="flex items-center gap-1 bg-purple-50 text-purple-700 text-sm px-3 py-1 rounded-full border border-purple-100">
                            {{ $min->name }} <span class="text-purple-400 text-xs">({{ $min->pivot->role }})</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 mb-4">Not assigned to any ministry.</p>
            @endif

            <p class="text-xs text-gray-500">Ministry leaders and membership are managed from each ministry view page.</p>
        </div>

        {{-- Leadership Roles --}}
        <div class="bg-white rounded-xl border shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Leadership Roles</h3>
            @if($member->leadershipRoles->isNotEmpty())
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($member->leadershipRoles as $role)
                        <div class="flex items-center gap-1 bg-amber-50 text-amber-700 text-sm px-3 py-1 rounded-full border border-amber-100">
                            {{ $role->name }}
                            @can('manage-leadership')
                                <form method="POST" action="{{ route('members.leadership.remove', $member) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="leadership_role_id" value="{{ $role->id }}">
                                    <button type="submit" class="ml-1 text-amber-400 hover:text-red-500">&times;</button>
                                </form>
                            @endcan
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 mb-4">No leadership role assigned.</p>
            @endif

            @can('manage-leadership')
                <form method="POST" action="{{ route('members.leadership.assign', $member) }}" class="flex gap-2 flex-wrap">
                    @csrf
                    <select name="leadership_role_id" required class="border rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Select role</option>
                        @foreach($leadershipRoles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="assigned_at" class="border rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <button type="submit" class="bg-amber-500 text-white text-sm px-3 py-1.5 rounded-lg hover:bg-amber-600 transition">Assign</button>
                </form>
            @endcan
        </div>
    </div>
</div>
@endsection
