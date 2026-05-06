@extends('layouts.app')

@section('title', 'New Branch')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('branches.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-gray-900">New Branch</h1>
    </div>

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('branches.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf

        {{-- Branch Info --}}
        <div>
            <h2 class="text-sm font-semibold text-indigo-600 uppercase tracking-wide mb-3">Branch Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Branch Name * @if($hq) <span class="text-gray-400 font-normal">(parent: {{ $hq->name }})</span>@endif
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @if(!$hq)
                        <p class="text-xs text-indigo-600 mt-1">This will be created as the <strong>Headquarters (HQ)</strong> branch.</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pastor</label>
                    <select name="pastor_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">— None —</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ old('pastor_id') == $member->id ? 'selected' : '' }}>{{ $member->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <hr class="border-gray-100">

        {{-- Address Info --}}
        <div>
            <h2 class="text-sm font-semibold text-indigo-600 uppercase tracking-wide mb-3">Address &amp; Contact</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <select id="countryCodeSelect" name="country_code_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select country</option>
                        @foreach($countryCodes as $countryCode)
                            <option value="{{ $countryCode->id }}" data-iso="{{ $countryCode->iso_code }}" @selected((string) old('country_code_id') === (string) $countryCode->id)>
                                {{ $countryCode->country_name }} (+{{ $countryCode->dial_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" name="city" value="{{ old('city') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" name="address" value="{{ old('address') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                {{-- Cameroon-specific administrative fields --}}
                <div id="cameroonFields" class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4 hidden">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Region</label>
                        <select id="regionSelect" name="region_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select region</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" @selected((string) old('region_id') === (string) $region->id)>
                                    {{ $region->item_number }}. {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Division</label>
                        <select id="divisionSelect" name="division_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select division</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subdivision <span class="text-gray-400">(Optional)</span></label>
                        <select id="subdivisionSelect" name="subdivision_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select subdivision</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-3 justify-end pt-2">
            <a href="{{ route('branches.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">Create Branch</button>
        </div>
    </form>
</div>

<script>
function toggleCameroonFields() {
    const select = document.getElementById('countryCodeSelect');
    const panel = document.getElementById('cameroonFields');
    if (!select || !panel) return;
    const opt = select.options[select.selectedIndex];
    const isCameroon = opt && opt.dataset.iso === 'CM';
    panel.classList.toggle('hidden', !isCameroon);
}

@php
    $divisionsJson = $divisions->map(fn($d) => ['id' => $d->id, 'name' => $d->name, 'item_number' => $d->item_number, 'region_id' => $d->region_id])->values();
    $subdivisionsJson = $subdivisions->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'item_number' => $s->item_number, 'division_id' => $s->division_id])->values();
@endphp
const allDivisions = @json($divisionsJson);
const allSubdivisions = @json($subdivisionsJson);

function populateDivisions(selectedDivisionId = '') {
    const regionId = document.getElementById('regionSelect')?.value;
    const divisionSelect = document.getElementById('divisionSelect');
    if (!divisionSelect) return;
    divisionSelect.innerHTML = '<option value="">Select division</option>';

    allDivisions
        .filter(d => String(d.region_id) === String(regionId))
        .forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.id;
            opt.textContent = `${d.item_number}. ${d.name}`;
            if (String(selectedDivisionId) === String(d.id)) opt.selected = true;
            divisionSelect.appendChild(opt);
        });
}

function populateSubdivisions(selectedSubdivisionId = '') {
    const divisionId = document.getElementById('divisionSelect')?.value;
    const subdivisionSelect = document.getElementById('subdivisionSelect');
    if (!subdivisionSelect) return;
    subdivisionSelect.innerHTML = '<option value="">Select subdivision</option>';

    allSubdivisions
        .filter(s => String(s.division_id) === String(divisionId))
        .forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = `${s.item_number}. ${s.name}`;
            if (String(selectedSubdivisionId) === String(s.id)) opt.selected = true;
            subdivisionSelect.appendChild(opt);
        });
}

document.getElementById('countryCodeSelect')?.addEventListener('change', toggleCameroonFields);
document.getElementById('regionSelect')?.addEventListener('change', () => {
    populateDivisions('');
    populateSubdivisions('');
});
document.getElementById('divisionSelect')?.addEventListener('change', () => populateSubdivisions(''));
toggleCameroonFields();
populateDivisions(@json(old('division_id')));
populateSubdivisions(@json(old('subdivision_id')));
</script>
@endsection
