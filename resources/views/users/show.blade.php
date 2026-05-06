@extends('layouts.app')
@section('page-title', 'User Detail')

@section('content')
<div class="max-w-xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
        <h2 class="text-lg font-semibold">{{ $user->member->full_name }}</h2>
        <a href="{{ route('users.edit', $user) }}" class="ml-auto text-sm bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 px-3 py-1.5 rounded-lg transition">Edit</a>
    </div>
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <dl class="space-y-4 text-sm">
            <div><dt class="text-gray-400">Member</dt>
                <dd><a href="{{ route('members.show', $user->member) }}" class="text-indigo-600 hover:underline font-medium">{{ $user->member->full_name }}</a></dd>
            </div>
            <div><dt class="text-gray-400">Email</dt><dd>{{ $user->email }}</dd></div>
            <div><dt class="text-gray-400">Role</dt>
                <dd><span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-full text-xs">{{ ucwords(str_replace('_', ' ', $user->role)) }}</span></dd>
            </div>
            <div><dt class="text-gray-400">Created</dt><dd>{{ $user->created_at->format('d M Y, H:i') }}</dd></div>
        </dl>
    </div>
</div>
@endsection
