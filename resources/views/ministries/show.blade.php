@extends('layouts.app')
@section('page-title', $ministry->name)

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('ministries.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
    <h2 class="text-lg font-semibold">{{ $ministry->name }}</h2>
    @can('manage-ministries')
        <a href="{{ route('ministries.edit', $ministry) }}" class="ml-auto text-sm bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 px-3 py-1.5 rounded-lg transition">Edit</a>
    @endcan
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl border shadow-sm p-6">
        <div class="flex items-center justify-between mb-4 gap-3">
            <h3 class="text-sm font-semibold text-gray-500 uppercase">Members ({{ $ministry->members->count() }})</h3>
            @can('manage-ministries')
                <div class="flex items-center gap-2">
                    <button type="button" onclick="openMinistryModal('member')" class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg transition">
                        Add Member
                    </button>
                    <button type="button" onclick="openMinistryModal('leader')" class="text-xs bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg transition">
                        Add Leader
                    </button>
                </div>
            @endcan
        </div>
        @forelse($ministry->members as $member)
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

    <div class="bg-white rounded-xl border shadow-sm p-6">
        <div class="flex items-center justify-between mb-4 gap-3">
            <h3 class="text-sm font-semibold text-gray-500 uppercase">Leaders</h3>
            @can('manage-ministries')
                <button type="button" onclick="openMinistryModal('leader')" class="text-xs bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg transition">
                    Add Leader
                </button>
            @endcan
        </div>
        @php
            $leaders = $ministry->members->filter(fn($m) => in_array($m->pivot->role, ['leader', 'assistant'], true));
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

    <div class="bg-white rounded-xl border shadow-sm p-6 lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-500 uppercase">Recent Reports</h3>
            <a href="{{ route('reports.index', ['ministry_id' => $ministry->id]) }}" class="text-xs text-indigo-600 hover:underline">View all</a>
        </div>
        @forelse($ministry->reports as $report)
            <div class="py-2 border-b last:border-0 text-sm">
                <a href="{{ route('reports.show', $report) }}" class="font-medium text-indigo-600 hover:underline">{{ $report->title }}</a>
                <p class="text-xs text-gray-400">{{ $report->created_at->format('d M Y') }} · {{ ucfirst($report->status) }}</p>
            </div>
        @empty
            <p class="text-sm text-gray-400">No reports yet.</p>
        @endforelse
    </div>
</div>

@can('manage-ministries')
    @php
        $assignedMemberIds = $ministry->members->pluck('id')->map(fn($id) => (int) $id)->all();
    @endphp
    <div id="addMinistryMemberModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="bg-white rounded-xl border shadow-xl w-full max-w-xl">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800 uppercase">Add Member to {{ $ministry->name }}</h3>
                <button type="button" onclick="document.getElementById('addMinistryMemberModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('ministries.members.assign', $ministry) }}" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="member_id" id="selectedMinistryMemberId">

                <div>
                    <label for="ministryMemberSearchInput" class="block text-sm font-medium text-gray-700 mb-1">Search member</label>
                    <input id="ministryMemberSearchInput" type="text" placeholder="Type first or last name..." class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>

                <div>
                    <p class="text-xs text-gray-500 mb-2">Select one member</p>
                    <div id="ministryMemberSearchResults" class="max-h-56 overflow-y-auto border rounded-lg divide-y bg-white">
                        @foreach($members as $candidate)
                            @if(!in_array((int) $candidate->id, $assignedMemberIds, true))
                                <button
                                    type="button"
                                    data-member-id="{{ $candidate->id }}"
                                    data-member-name="{{ strtolower($candidate->full_name) }}"
                                    class="ministry-member-option w-full text-left px-3 py-2 text-sm hover:bg-indigo-50 transition"
                                >
                                    {{ $candidate->full_name }}
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" id="ministryRoleSelect" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="member">Member</option>
                        <option value="assistant">Assistant</option>
                        <option value="leader">Leader</option>
                    </select>
                </div>

                <div class="border rounded-lg p-3 bg-amber-50/40">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xs text-gray-600">Optional local ministry title for this member.</p>
                        <button type="button" id="toggleMinistryCustomRole" class="text-xs text-indigo-600 hover:underline">Create Custom Title</button>
                    </div>

                    <div id="ministryCustomRoleWrap" class="hidden mt-3">
                        <label for="customMinistryRoleTitle" class="block text-sm font-medium text-gray-700 mb-1">Custom Ministry Title</label>
                        <input
                            id="customMinistryRoleTitle"
                            type="text"
                            name="custom_role_title"
                            maxlength="120"
                            placeholder="e.g. Choir Flow Coordinator"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        >
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('addMinistryMemberModal').classList.add('hidden')" class="text-sm text-gray-500 hover:underline">Cancel</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded-lg">Add Member</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    (function () {
        const modal = document.getElementById('addMinistryMemberModal');
        if (!modal) return;

        const searchInput = document.getElementById('ministryMemberSearchInput');
        const selectedMemberId = document.getElementById('selectedMinistryMemberId');
        const roleSelect = document.getElementById('ministryRoleSelect');
        const toggleCustomRoleBtn = document.getElementById('toggleMinistryCustomRole');
        const customRoleWrap = document.getElementById('ministryCustomRoleWrap');
        const customRoleInput = document.getElementById('customMinistryRoleTitle');
        const options = Array.from(modal.querySelectorAll('.ministry-member-option'));

        window.openMinistryModal = function(defaultRole = 'member') {
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
