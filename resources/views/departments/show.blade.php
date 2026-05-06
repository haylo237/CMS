@extends('layouts.app')
@section('page-title', $department->name)

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('departments.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
    <h2 class="text-lg font-semibold">{{ $department->name }}</h2>
    <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-600 capitalize">{{ $department->type }}</span>
    @can('manage-departments')
        <a href="{{ route('departments.edit', $department) }}" class="ml-auto text-sm bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 px-3 py-1.5 rounded-lg transition">Edit</a>
    @endcan
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Members --}}
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <div class="flex items-center justify-between mb-4 gap-3">
            <h3 class="text-sm font-semibold text-gray-500 uppercase">Members ({{ $department->members->count() }})</h3>
            @can('manage-departments')
                <div class="flex items-center gap-2">
                    <button type="button" onclick="openDepartmentModal('member')" class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg transition">
                        Add Member
                    </button>
                    <button type="button" onclick="openDepartmentModal('head')" class="text-xs bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg transition">
                        Add Leader
                    </button>
                </div>
            @endcan
        </div>
        @forelse($department->members as $member)
            <div class="flex items-center justify-between py-2 border-b last:border-0 text-sm">
                <a href="{{ route('members.show', $member) }}" class="font-medium text-indigo-600 hover:underline">{{ $member->full_name }}</a>
                <div class="text-right">
                    <span class="capitalize text-gray-400 text-xs">{{ $member->pivot->role }}</span>
                    @if($member->pivot->custom_role_title)
                        <p class="text-[11px] text-amber-600">{{ $member->pivot->custom_role_title }}</p>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400">No members assigned.</p>
        @endforelse
    </div>

    {{-- Leaders --}}
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <div class="flex items-center justify-between mb-4 gap-3">
            <h3 class="text-sm font-semibold text-gray-500 uppercase">Leaders</h3>
            @can('manage-departments')
                <button type="button" onclick="openDepartmentModal('head')" class="text-xs bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg transition">
                    Add Leader
                </button>
            @endcan
        </div>
        @php
            $leaders = $department->members->filter(fn($m) => in_array($m->pivot->role, ['head', 'assistant'], true));
        @endphp
        @forelse($leaders as $leader)
            <div class="flex items-center justify-between py-2 border-b last:border-0 text-sm">
                <a href="{{ route('members.show', $leader) }}" class="font-medium text-indigo-600 hover:underline">{{ $leader->full_name }}</a>
                <div class="text-right">
                    <span class="capitalize text-gray-400 text-xs">{{ $leader->pivot->role }}</span>
                    @if($leader->pivot->custom_role_title)
                        <p class="text-[11px] text-amber-600">{{ $leader->pivot->custom_role_title }}</p>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400">No leaders assigned yet.</p>
        @endforelse
    </div>

    {{-- Recent Reports --}}
    <div class="bg-white rounded-xl border shadow-sm p-6 lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-500 uppercase">Recent Reports</h3>
            <a href="{{ route('reports.index', ['department_id' => $department->id]) }}" class="text-xs text-indigo-600 hover:underline">View all</a>
        </div>
        @forelse($department->reports as $report)
            <div class="py-2 border-b last:border-0 text-sm">
                <a href="{{ route('reports.show', $report) }}" class="font-medium text-indigo-600 hover:underline">{{ $report->title }}</a>
                <p class="text-xs text-gray-400">{{ $report->created_at->format('d M Y') }} · {{ ucfirst($report->status) }}</p>
            </div>
        @empty
            <p class="text-sm text-gray-400">No reports yet.</p>
        @endforelse
    </div>
</div>

@can('manage-departments')
    @php
        $assignedMemberIds = $department->members->pluck('id')->map(fn($id) => (int) $id)->all();
    @endphp
    <div id="addMemberModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="bg-white rounded-xl border shadow-xl w-full max-w-xl">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800 uppercase">Add Member to {{ $department->name }}</h3>
                <button type="button" onclick="document.getElementById('addMemberModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('departments.members.assign', $department) }}" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="member_id" id="selectedMemberId">

                <div>
                    <label for="memberSearchInput" class="block text-sm font-medium text-gray-700 mb-1">Search member</label>
                    <input id="memberSearchInput" type="text" placeholder="Type first or last name..." class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>

                <div>
                    <p class="text-xs text-gray-500 mb-2">Select one member</p>
                    <div id="memberSearchResults" class="max-h-56 overflow-y-auto border rounded-lg divide-y bg-white">
                        @foreach($members as $candidate)
                            @if(!in_array((int) $candidate->id, $assignedMemberIds, true))
                                <button
                                    type="button"
                                    data-member-id="{{ $candidate->id }}"
                                    data-member-name="{{ strtolower($candidate->full_name) }}"
                                    class="member-option w-full text-left px-3 py-2 text-sm hover:bg-indigo-50 transition"
                                >
                                    {{ $candidate->full_name }}
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" id="departmentRoleSelect" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="member">Member</option>
                        <option value="assistant">Assistant</option>
                        <option value="head">Head (Leader)</option>
                    </select>
                </div>

                <div class="border rounded-lg p-3 bg-amber-50/40">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xs text-gray-600">Optional local department title for this member.</p>
                        <button type="button" id="toggleDepartmentCustomRole" class="text-xs text-indigo-600 hover:underline">Create Custom Title</button>
                    </div>

                    <div id="departmentCustomRoleWrap" class="hidden mt-3">
                        <label for="customDepartmentRoleTitle" class="block text-sm font-medium text-gray-700 mb-1">Custom Department Title</label>
                        <input
                            id="customDepartmentRoleTitle"
                            type="text"
                            name="custom_role_title"
                            maxlength="120"
                            placeholder="e.g. Prayer Coordinator"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        >
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('addMemberModal').classList.add('hidden')" class="text-sm text-gray-500 hover:underline">Cancel</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded-lg">Add Member</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    (function () {
        const modal = document.getElementById('addMemberModal');
        if (!modal) return;

        const searchInput = document.getElementById('memberSearchInput');
        const selectedMemberId = document.getElementById('selectedMemberId');
        const roleSelect = document.getElementById('departmentRoleSelect');
        const toggleCustomRoleBtn = document.getElementById('toggleDepartmentCustomRole');
        const customRoleWrap = document.getElementById('departmentCustomRoleWrap');
        const customRoleInput = document.getElementById('customDepartmentRoleTitle');
        const options = Array.from(modal.querySelectorAll('.member-option'));

        window.openDepartmentModal = function(defaultRole = 'member') {
            modal.classList.remove('hidden');
            if (roleSelect) {
                roleSelect.value = defaultRole;
            }
            if (customRoleInput) {
                customRoleInput.value = '';
            }
            if (customRoleWrap) {
                customRoleWrap.classList.add('hidden');
            }
            if (toggleCustomRoleBtn) {
                toggleCustomRoleBtn.textContent = 'Create Custom Title';
            }
        };

        toggleCustomRoleBtn?.addEventListener('click', () => {
            if (!customRoleWrap) return;

            customRoleWrap.classList.toggle('hidden');
            const isVisible = !customRoleWrap.classList.contains('hidden');
            toggleCustomRoleBtn.textContent = isVisible ? 'Hide Custom Title' : 'Create Custom Title';

            if (isVisible) {
                customRoleInput?.focus();
            } else if (customRoleInput) {
                customRoleInput.value = '';
            }
        });

        function applySearchFilter() {
            const query = (searchInput?.value || '').trim().toLowerCase();
            options.forEach((option) => {
                const name = option.dataset.memberName || '';
                option.classList.toggle('hidden', query !== '' && !name.includes(query));
            });
        }

        function clearSelectionStyles() {
            options.forEach((option) => option.classList.remove('bg-indigo-100', 'font-medium'));
        }

        options.forEach((option) => {
            option.addEventListener('click', () => {
                clearSelectionStyles();
                option.classList.add('bg-indigo-100', 'font-medium');
                selectedMemberId.value = option.dataset.memberId || '';
            });
        });

        searchInput?.addEventListener('input', applySearchFilter);

        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });
    })();
    </script>
@endcan
@endsection
