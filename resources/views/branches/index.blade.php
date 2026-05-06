@extends('layouts.app')

@section('title', 'Branches')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Branches</h1>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('branches.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <i class="fa-solid fa-plus"></i> New Branch
        </a>
    @endif
</div>

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse($branches as $branch)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $branch->name }}</h2>
                    @if($branch->city)
                        <p class="text-sm text-gray-500 mt-0.5"><i class="fa-solid fa-location-dot mr-1"></i>{{ $branch->city }}</p>
                    @endif
                    @if($branch->parentBranch)
                        <p class="text-xs text-indigo-600 mt-1">Sub-branch of {{ $branch->parentBranch->name }}</p>
                    @endif
                </div>
                <span class="text-xs bg-indigo-50 text-indigo-700 font-medium px-2 py-1 rounded-full">{{ $branch->members_count }} members</span>
            </div>

            @if($branch->pastor)
                <p class="mt-3 text-sm text-gray-600"><i class="fa-solid fa-user-tie mr-1 text-gray-400"></i>Pastor: {{ $branch->pastor->full_name }}</p>
            @endif

            @if($branch->description)
                <p class="mt-2 text-sm text-gray-500 line-clamp-2">{{ $branch->description }}</p>
            @endif

            <div class="mt-4 flex items-center gap-2">
                <a href="{{ route('branches.show', $branch) }}" class="text-indigo-600 hover:underline text-sm">View</a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('branches.edit', $branch) }}" class="text-yellow-600 hover:underline text-sm">Edit</a>
                @if(auth()->user()->isAdmin())
                    <span class="text-gray-300">|</span>
                    <form method="POST" action="{{ route('branches.destroy', $branch) }}" onsubmit="return confirm('Delete this branch?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:underline text-sm">Delete</button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="col-span-3 text-center py-16 text-gray-400">
            <i class="fa-solid fa-code-branch text-4xl mb-3"></i>
            <p>No branches yet.@if(auth()->user()->isAdmin()) <a href="{{ route('branches.create') }}" class="text-indigo-600 underline">Add one</a>. @endif</p>
        </div>
    @endforelse
</div>

<div class="mt-6">{{ $branches->links() }}</div>
@endsection
