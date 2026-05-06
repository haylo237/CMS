@extends('layouts.app')
@section('page-title', 'Ministries')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-semibold">All Ministries</h2>
    @can('manage-ministries')
        <a href="{{ route('ministries.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">+ Add Ministry</a>
    @endcan
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($ministries as $ministry)
        <div class="bg-white rounded-xl border shadow-sm p-5 hover:shadow-md transition cursor-pointer"
             onclick="window.location='{{ route('ministries.show', $ministry) }}'">
            <div class="flex items-start justify-between mb-3">
                <h3 class="font-semibold text-gray-800">{{ $ministry->name }}</h3>
                @can('manage-ministries')
                    <div class="flex gap-2 text-gray-400">
                        <a href="{{ route('ministries.edit', $ministry) }}" onclick="event.stopPropagation()" class="hover:text-amber-500 transition"><i class="fa-solid fa-pen-to-square"></i></a>
                        <form method="POST" action="{{ route('ministries.destroy', $ministry) }}" onsubmit="event.stopPropagation(); return confirm('Delete ministry?')">
                            @csrf @method('DELETE')
                            <button class="hover:text-red-500 transition"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                @endcan
            </div>
            <p class="text-sm text-gray-500 mb-4">{{ $ministry->description ?? 'No description' }}</p>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500"><i class="fa-solid fa-users mr-1"></i> {{ $ministry->members_count }} members</span>
                <a href="{{ route('ministries.show', $ministry) }}" onclick="event.stopPropagation()" class="text-indigo-600 hover:underline">View →</a>
            </div>
        </div>
    @empty
        <div class="col-span-3 text-center text-gray-400 py-16">No ministries yet.</div>
    @endforelse
</div>
@endsection
