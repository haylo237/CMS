@extends('layouts.app')

@section('title', 'New Announcement')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('announcements.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-gray-900">New Announcement</h1>
    </div>

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('announcements.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Body *</label>
            <textarea name="body" rows="5" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('body') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Audience *</label>
                <select name="audience" id="audience" required onchange="toggleAudienceFields()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="all" {{ old('audience') == 'all' ? 'selected' : '' }}>All Members</option>
                    <option value="branch" {{ old('audience') == 'branch' ? 'selected' : '' }}>Branch</option>
                    <option value="department" {{ old('audience') == 'department' ? 'selected' : '' }}>Department</option>
                    <option value="ministry" {{ old('audience') == 'ministry' ? 'selected' : '' }}>Ministry</option>
                </select>
            </div>

            <div id="branch_field" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                <select name="branch_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">— Select —</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="department_field" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select name="department_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">— Select —</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="ministry_field" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ministry</label>
                <select name="ministry_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">— Select —</option>
                    @foreach($ministries as $m)
                        <option value="{{ $m->id }}" {{ old('ministry_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Publish At</label>
                <input type="datetime-local" name="published_at" value="{{ old('published_at') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Expires At</label>
                <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <div class="flex gap-3 justify-end pt-2">
            <a href="{{ route('announcements.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">Publish</button>
        </div>
    </form>
</div>

<script>
function toggleAudienceFields() {
    const val = document.getElementById('audience').value;
    document.getElementById('branch_field').classList.toggle('hidden', val !== 'branch');
    document.getElementById('department_field').classList.toggle('hidden', val !== 'department');
    document.getElementById('ministry_field').classList.toggle('hidden', val !== 'ministry');
}
document.addEventListener('DOMContentLoaded', toggleAudienceFields);
</script>
@endsection
