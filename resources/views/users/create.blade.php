@extends('layouts.app')
@section('page-title', 'Create User')

@section('content')
<div class="max-w-xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
        <h2 class="text-lg font-semibold">New User Account</h2>
    </div>
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Member <span class="text-red-500">*</span></label>
                <select name="member_id" required class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('member_id') border-red-400 @enderror">
                    <option value="">Select member</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>{{ $member->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('email') border-red-400 @enderror">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                <select name="role" required class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    @foreach(App\Models\User::ROLES as $role)
                        <option value="{{ $role }}" @selected(old('role') === $role)>{{ ucwords(str_replace('_', ' ', $role)) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" required minlength="8" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" required class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">Create User</button>
                <a href="{{ route('users.index') }}" class="text-sm text-gray-500 hover:underline py-2.5">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
