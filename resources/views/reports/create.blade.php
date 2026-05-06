@extends('layouts.app')
@section('page-title', 'New Report')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('reports.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
        <h2 class="text-lg font-semibold">New Report</h2>
    </div>
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <form method="POST" action="{{ route('reports.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Report Type <span class="text-red-500">*</span></label>
                <select name="report_type" required id="report_type" onchange="toggleTypeFields(this.value)"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Select type</option>
                    @foreach(['department','ministry','finance'] as $t)
                        <option value="{{ $t }}" @selected(old('report_type') === $t)>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>

            <div id="dept_field" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Department <span class="text-red-500">*</span></label>
                <select name="department_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Select department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" @selected(old('department_id') == $dept->id)>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="min_field" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ministry <span class="text-red-500">*</span></label>
                <select name="ministry_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Select ministry</option>
                    @foreach($ministries as $min)
                        <option value="{{ $min->id }}" @selected(old('ministry_id') == $min->id)>{{ $min->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Period Start <span class="text-red-500">*</span></label>
                    <input type="date" name="reporting_period_start" value="{{ old('reporting_period_start') }}" required class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Period End <span class="text-red-500">*</span></label>
                    <input type="date" name="reporting_period_end" value="{{ old('reporting_period_end') }}" required class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Metadata (JSON)</label>
                <p class="text-xs text-gray-400 mb-1">e.g. {"attendance": 120, "activities": ["Rehearsal"]}</p>
                <textarea name="metadata" rows="3" placeholder='{"attendance": 120}' class="w-full border rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('metadata') }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">Save Draft</button>
                <a href="{{ route('reports.index') }}" class="text-sm text-gray-500 hover:underline py-2.5">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleTypeFields(type) {
    document.getElementById('dept_field').classList.toggle('hidden', type !== 'department');
    document.getElementById('min_field').classList.toggle('hidden', type !== 'ministry');
}
document.addEventListener('DOMContentLoaded', () => {
    toggleTypeFields(document.getElementById('report_type').value);
});
</script>
@endsection
