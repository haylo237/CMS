@extends('layouts.app')
@section('page-title', 'Add Leadership Role')

@section('content')
<div class="max-w-xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('leadership.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
        <h2 class="text-lg font-semibold">New Leadership Role</h2>
    </div>
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <form method="POST" action="{{ route('leadership.store') }}" class="space-y-5">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('name') border-red-400 @enderror">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rank <span class="text-red-500">*</span></label>
                    <input type="number" name="rank" value="{{ old('rank', 99) }}" min="1" required class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('description') }}</textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">Save</button>
                <a href="{{ route('leadership.index') }}" class="text-sm text-gray-500 hover:underline py-2.5">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
