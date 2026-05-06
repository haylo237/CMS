@extends('layouts.app')
@section('page-title', 'Edit Member')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('members.show', $member) }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
        <h2 class="text-lg font-semibold">Edit: {{ $member->full_name }}</h2>
    </div>

    <div class="bg-white rounded-xl border shadow-sm p-6">
        <form method="POST" action="{{ route('members.update', $member) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Branch <span class="text-red-500">*</span></label>
                @if(auth()->user()?->isPastor())
                    <input type="text" value="{{ auth()->user()->pastoredBranch()?->name }}" disabled class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-600">
                    <input type="hidden" name="branch_id" value="{{ auth()->user()->pastoredBranchId() }}">
                @else
                    <select name="branch_id" required class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('branch_id') border-red-400 @enderror">
                        <option value="">Select branch</option>
                        @foreach($branches as $branchOption)
                            <option value="{{ $branchOption->id }}" @selected((string) old('branch_id', $member->branch_id) === (string) $branchOption->id)>{{ $branchOption->name }} @if($branchOption->parentBranch)({{ $branchOption->parentBranch->name }})@endif</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                <div class="flex items-center gap-4">
                    @if($member->profile_photo_url)
                        <img src="{{ $member->profile_photo_url }}" alt="{{ $member->full_name }}" class="w-14 h-14 rounded-full object-cover border">
                    @else
                        <div class="w-14 h-14 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center border">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    @endif
                    <div class="flex-1">
                        <input type="file" name="profile_photo" accept="image/png,image/jpeg,image/webp"
                               class="w-full border rounded-lg px-3 py-2 text-sm file:mr-3 file:px-3 file:py-1.5 file:rounded-md file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @error('profile_photo') border-red-400 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Upload a new image to replace current photo. Max 2MB.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name', $member->first_name) }}" required
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name', $member->last_name) }}" required
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $member->email) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('email') border-red-400 @enderror">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Country Code</label>
                    <select name="country_code_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('country_code_id') border-red-400 @enderror">
                        <option value="">Select code</option>
                        @foreach($countryCodes as $countryCode)
                            <option value="{{ $countryCode->id }}" @selected((string) old('country_code_id', $member->country_code_id) === (string) $countryCode->id)>
                                +{{ $countryCode->dial_code }} ({{ $countryCode->country_name }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $member->phone) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                    <select name="gender" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Select gender</option>
                        @foreach(['male','female','other'] as $g)
                            <option value="{{ $g }}" @selected(old('gender', $member->gender) === $g)>{{ ucfirst($g) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $member->date_of_birth?->toDateString()) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" required class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    @foreach(['active','inactive','deceased','transferred'] as $s)
                        <option value="{{ $s }}" @selected(old('status', $member->status) === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('address', $member->address) }}</textarea>
            </div>

            <div class="border-t mt-2 pt-6 space-y-4">
                <h3 class="text-sm font-semibold text-gray-600 uppercase">Leadership Roles</h3>

                @if($member->leadershipRoles->isNotEmpty())
                    <div class="flex flex-wrap gap-3">
                        @foreach($member->leadershipRoles as $role)
                            <div class="flex items-center gap-2 bg-amber-50 text-amber-700 text-sm px-3 py-1.5 rounded-full border border-amber-100">
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
                    <p class="text-sm text-gray-400">No leadership role assigned.</p>
                @endif

                @can('manage-leadership')
                    <form method="POST" action="{{ route('members.leadership.assign', $member) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                        @csrf
                        <select name="leadership_role_id" required class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="">Select role</option>
                            @foreach($leadershipRoles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <input type="date" name="assigned_at" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">Assign Role</button>
                    </form>
                @endcan
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">Update</button>
                <a href="{{ route('members.show', $member) }}" class="text-sm text-gray-500 hover:underline py-2.5">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
