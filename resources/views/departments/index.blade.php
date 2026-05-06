@extends('layouts.app')
@section('page-title', 'Departments')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-semibold">All Departments</h2>
    @can('manage-departments')
        <a href="{{ route('departments.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">+ Add Department</a>
    @endcan
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($departments as $dept)
        <div class="bg-white rounded-xl border shadow-sm p-5 hover:shadow-md transition cursor-pointer"
             onclick="window.location='{{ route('departments.show', $dept) }}'">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $dept->name }}</h3>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-600 capitalize">{{ $dept->type }}</span>
                </div>
                <div class="flex gap-2 text-gray-400">
                    @can('manage-departments')
                        <a href="{{ route('departments.edit', $dept) }}" onclick="event.stopPropagation()" class="hover:text-amber-500 transition"><i class="fa-solid fa-pen-to-square"></i></a>
                        <form method="POST" action="{{ route('departments.destroy', $dept) }}" onsubmit="event.stopPropagation(); return confirm('Delete department?')">
                            @csrf @method('DELETE')
                            <button class="hover:text-red-500 transition"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    @endcan
                </div>
            </div>
            <p class="text-sm text-gray-500 mb-4">{{ $dept->description ?? 'No description' }}</p>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500"><i class="fa-solid fa-users mr-1"></i> {{ $dept->members_count }} members</span>
                <a href="{{ route('departments.show', $dept) }}" onclick="event.stopPropagation()" class="text-indigo-600 hover:underline">View →</a>
            </div>
        </div>
    @empty
        <div class="col-span-3 text-center text-gray-400 py-16">No departments yet.</div>
    @endforelse
</div>
@endsection
