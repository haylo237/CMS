@extends('layouts.app')

@section('title', 'Settings')

@section('content')
@php
    $activeTab = session('tab', request('tab', 'general'));
    $s = fn(string $key, $default = '') => \App\Models\Setting::get($key, $default);
    $selectedCurrencyId = (int) ($s('currency_id') ?: \App\Models\Setting::currentCurrency()?->id);
@endphp

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
@endif

{{-- Tab bar --}}
<div class="border-b border-gray-200 mb-6 flex gap-1" id="tabBar">
    @php
        $tabs = [
            'general'       => ['icon' => 'fa-sliders',         'label' => 'General'],
            'branding'      => ['icon' => 'fa-palette',         'label' => 'Branding'],
            'finance'       => ['icon' => 'fa-coins',           'label' => 'Finance'],
            'notifications' => ['icon' => 'fa-bell',            'label' => 'Notifications'],
            'templates'     => ['icon' => 'fa-file-lines',      'label' => 'Document Templates'],
            'whatsapp'      => ['icon' => 'fa-brands fa-whatsapp', 'label' => 'WhatsApp'],
        ];
    @endphp
    @foreach($tabs as $key => $tab)
        <button type="button" onclick="switchTab('{{ $key }}')" data-tab="{{ $key }}"
            class="tab-btn flex items-center gap-2 px-4 py-2.5 text-sm font-medium border-b-2 transition -mb-px
                {{ $activeTab === $key ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            <i class="fa-solid {{ $tab['icon'] }}"></i>{{ $tab['label'] }}
        </button>
    @endforeach
</div>

{{-- ═══════════════════════ GENERAL ═══════════════════════ --}}
<div id="tab-general" class="tab-panel {{ $activeTab !== 'general' ? 'hidden' : '' }}">
    <form method="POST" action="{{ route('settings.general') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Church Name</label>
                <input type="text" name="church_name" value="{{ $s('church_name') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                <input type="text" name="church_city" value="{{ $s('church_city') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Church Phone Country Code</label>
                <select name="church_country_code_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Select code</option>
                    @foreach($countryCodes as $countryCode)
                        <option value="{{ $countryCode->id }}" @selected((string) $s('church_country_code_id') === (string) $countryCode->id)>
                            +{{ $countryCode->dial_code }} ({{ $countryCode->country_name }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="church_phone" value="{{ $s('church_phone') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="church_email" value="{{ $s('church_email') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                <input type="text" name="church_website" value="{{ $s('church_website') }}" placeholder="https://" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="church_address" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ $s('church_address') }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Motto / Tagline</label>
                <input type="text" name="church_motto" value="{{ $s('church_motto') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                <select name="currency_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" @selected($selectedCurrencyId === $currency->id)>
                            {{ $currency->name }} ({{ $currency->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                <select name="timezone" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach(['Africa/Lagos','Africa/Accra','Africa/Nairobi','Africa/Cairo','Europe/London','America/New_York','America/Chicago','America/Los_Angeles','Asia/Kolkata','Asia/Singapore'] as $tz)
                        <option value="{{ $tz }}" {{ $s('timezone','Africa/Lagos') === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex justify-end pt-5">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg">Save General Settings</button>
        </div>
    </form>
</div>

{{-- ═══════════════════════ BRANDING ═══════════════════════ --}}
<div id="tab-branding" class="tab-panel {{ $activeTab !== 'branding' ? 'hidden' : '' }}">
    <form method="POST" action="{{ route('settings.branding') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @csrf
        {{-- Logo upload --}}
        <div class="mb-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Church Logo</h2>
            <div class="flex items-start gap-6">
                <div class="shrink-0">
                    @if($s('church_logo'))
                        <img src="{{ Storage::url($s('church_logo')) }}" alt="Church Logo"
                             class="w-28 h-28 object-contain rounded-xl border border-gray-200 p-2 bg-gray-50" id="logoPreview">
                    @else
                        <div class="w-28 h-28 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50" id="logoPlaceholder">
                            <i class="fa-solid fa-image text-3xl text-gray-300"></i>
                        </div>
                        <img src="" alt="" class="w-28 h-28 object-contain rounded-xl border border-gray-200 p-2 bg-gray-50 hidden" id="logoPreview">
                    @endif
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Logo</label>
                    <p class="text-xs text-gray-500 mb-3">Recommended: PNG with transparent background, min 200×200px, max 2MB.</p>
                    <label class="cursor-pointer inline-flex items-center gap-2 border border-gray-300 rounded-lg px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                        <i class="fa-solid fa-upload text-gray-400"></i> Choose file
                        <input type="file" name="church_logo" accept="image/*" class="hidden" id="logoInput" onchange="previewLogo(this)">
                    </label>
                    <span class="ml-2 text-xs text-gray-500" id="logoFileName">No file chosen</span>
                    @if($s('church_logo'))
                        <p class="text-xs text-gray-400 mt-2">Current logo is displayed. Upload a new file to replace it.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-6 mb-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Colors</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Primary / Accent Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="primary_color" value="{{ $s('primary_color', '#4f46e5') }}"
                               class="w-10 h-10 rounded border border-gray-300 cursor-pointer p-0.5">
                        <input type="text" id="primary_color_text" value="{{ $s('primary_color', '#4f46e5') }}"
                               class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-32"
                               oninput="document.querySelector('[name=primary_color]').value=this.value">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Report Header Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="report_header_bg" value="{{ $s('report_header_bg', '#4f46e5') }}"
                               class="w-10 h-10 rounded border border-gray-300 cursor-pointer p-0.5">
                        <input type="text" id="report_header_bg_text" value="{{ $s('report_header_bg', '#4f46e5') }}"
                               class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-32"
                               oninput="document.querySelector('[name=report_header_bg]').value=this.value">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg">Save Branding</button>
        </div>
    </form>
</div>

{{-- ═══════════════════════ FINANCE ═══════════════════════ --}}
<div id="tab-finance" class="tab-panel {{ $activeTab !== 'finance' ? 'hidden' : '' }}">
    <form method="POST" action="{{ route('settings.finance') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 max-w-lg">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fiscal Year Start (MM-DD)</label>
                <input type="text" name="fiscal_year_start" value="{{ $s('fiscal_year_start', '01-01') }}"
                       placeholder="01-01" pattern="\d{2}-\d{2}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <p class="text-xs text-gray-400 mt-1">Format: MM-DD, e.g. 01-01 for January 1st.</p>
            </div>
            <div class="flex items-center gap-3 pt-5">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="finance_approval" value="0">
                    <input type="checkbox" name="finance_approval" value="1" {{ $s('finance_approval') ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
                <span class="text-sm text-gray-700">Require approval for finance entries</span>
            </div>
        </div>
        <div class="flex justify-end pt-5">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg">Save Finance Settings</button>
        </div>
    </form>
</div>

{{-- ═══════════════════════ NOTIFICATIONS ═══════════════════════ --}}
<div id="tab-notifications" class="tab-panel {{ $activeTab !== 'notifications' ? 'hidden' : '' }}">
    <form method="POST" action="{{ route('settings.notifications') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @csrf
        <div class="space-y-4 max-w-md">
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                <div>
                    <p class="text-sm font-medium text-gray-800">New Member Registration</p>
                    <p class="text-xs text-gray-500">Send an in-app notification when a new member is added.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="notify_new_member" value="1" {{ $s('notify_new_member') ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
            </div>
            <div class="flex items-center justify-between py-3">
                <div>
                    <p class="text-sm font-medium text-gray-800">New Report Submitted</p>
                    <p class="text-xs text-gray-500">Send an in-app notification when a report is submitted for review.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="notify_new_report" value="1" {{ $s('notify_new_report') ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
            </div>
        </div>
        <div class="flex justify-end pt-5">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg">Save Notification Settings</button>
        </div>
    </form>
</div>

{{-- ═══════════════════════ DOCUMENT TEMPLATES ═══════════════════════ --}}
<div id="tab-templates" class="tab-panel {{ $activeTab !== 'templates' ? 'hidden' : '' }}">

    {{-- Upload form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Upload New Template</h2>
        <form method="POST" action="{{ route('settings.templates.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Template Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                    <select name="type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @foreach(['report' => 'Report','certificate' => 'Certificate','letter' => 'Letter','receipt' => 'Receipt','other' => 'Other'] as $val => $label)
                            <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">File *</label>
                    <p class="text-xs text-gray-500 mb-2">Accepted: PDF, DOC, DOCX, ODT, XLS, XLSX, ODS, PPTX, PNG, JPG — max 10MB</p>
                    <label class="cursor-pointer inline-flex items-center gap-2 border border-gray-300 rounded-lg px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fa-solid fa-paperclip text-gray-400"></i> Choose file
                        <input type="file" name="file" required class="hidden" id="templateFile" onchange="document.getElementById('templateFileName').textContent=this.files[0]?.name||'No file chosen'">
                    </label>
                    <span class="ml-2 text-xs text-gray-500" id="templateFileName">No file chosen</span>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('description') }}</textarea>
                </div>
                <div class="sm:col-span-2 flex items-center gap-2">
                    <input type="checkbox" name="is_default" value="1" id="is_default" class="rounded border-gray-300 text-indigo-600">
                    <label for="is_default" class="text-sm text-gray-700">Set as default template for this type</label>
                </div>
            </div>
            <div class="flex justify-end pt-4">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg flex items-center gap-2">
                    <i class="fa-solid fa-upload"></i> Upload Template
                </button>
            </div>
        </form>
    </div>

    {{-- Templates list grouped by type --}}
    @php
        $typeLabels = ['report' => 'Report', 'certificate' => 'Certificate', 'letter' => 'Letter', 'receipt' => 'Receipt', 'other' => 'Other'];
        $typeIcons  = ['report' => 'fa-chart-bar', 'certificate' => 'fa-award', 'letter' => 'fa-envelope-open-text', 'receipt' => 'fa-receipt', 'other' => 'fa-file'];
        $typeColors = ['report' => 'bg-blue-50 text-blue-700', 'certificate' => 'bg-amber-50 text-amber-700', 'letter' => 'bg-green-50 text-green-700', 'receipt' => 'bg-purple-50 text-purple-700', 'other' => 'bg-gray-100 text-gray-700'];
    @endphp

    @if($templates->isEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-6 py-12 text-center text-gray-400">
            <i class="fa-solid fa-file-lines text-4xl mb-3"></i>
            <p>No templates uploaded yet.</p>
        </div>
    @else
        @foreach($typeLabels as $type => $label)
            @if(isset($templates[$type]) && $templates[$type]->count())
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <i class="fa-solid {{ $typeIcons[$type] }}"></i> {{ $label }} Templates
                    </h3>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-gray-600">Name</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-600">File</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-600">Size</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-600">Uploaded by</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-600">Uploaded</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($templates[$type] as $tpl)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <span class="font-medium text-gray-900">{{ $tpl->name }}</span>
                                            @if($tpl->is_default)
                                                <span class="ml-2 text-xs bg-indigo-50 text-indigo-700 font-medium px-1.5 py-0.5 rounded">default</span>
                                            @endif
                                            @if($tpl->description)
                                                <p class="text-xs text-gray-400 mt-0.5">{{ $tpl->description }}</p>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">
                                            <a href="{{ Storage::url($tpl->file_path) }}" target="_blank" class="text-indigo-600 hover:underline flex items-center gap-1">
                                                <i class="fa-solid fa-arrow-down-to-line text-xs"></i>{{ $tpl->original_name }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-gray-500">{{ $tpl->file_size_human }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $tpl->uploadedBy?->full_name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-gray-500">{{ $tpl->created_at->format('M d, Y') }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                @unless($tpl->is_default)
                                                    <form method="POST" action="{{ route('settings.templates.default', $tpl) }}">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="text-xs text-indigo-600 hover:underline">Set default</button>
                                                    </form>
                                                    <span class="text-gray-200">|</span>
                                                @endunless
                                                <form method="POST" action="{{ route('settings.templates.destroy', $tpl) }}" onsubmit="return confirm('Delete this template?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-xs text-red-500 hover:underline">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endforeach
    @endif
</div>

{{-- ═══════════════════════ WHATSAPP ═══════════════════════ --}}
<div id="tab-whatsapp" class="tab-panel {{ $activeTab !== 'whatsapp' ? 'hidden' : '' }}">
    <form method="POST" action="{{ route('settings.whatsapp') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @csrf
        <h2 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-brands fa-whatsapp text-green-500 text-lg"></i> WhatsApp Business Cloud API
        </h2>
        <p class="text-sm text-gray-500 mb-5">
            Configure the Meta WhatsApp Business Cloud API to enable broadcast messages to members.
            <a href="https://developers.facebook.com/docs/whatsapp/cloud-api/get-started" target="_blank" class="text-indigo-600 hover:underline">Setup guide &rarr;</a>
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <label class="flex items-center gap-2 cursor-pointer w-fit">
                    <span class="relative inline-flex h-6 w-11 items-center rounded-full transition
                        {{ $s('whatsapp_enabled','0') == '1' ? 'bg-green-500' : 'bg-gray-200' }}" id="waToggleBg">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition
                            {{ $s('whatsapp_enabled','0') == '1' ? 'translate-x-6' : 'translate-x-1' }}" id="waToggleKnob"></span>
                    </span>
                    <span class="text-sm font-medium text-gray-700">Enable WhatsApp Broadcasting</span>
                    <input type="hidden" name="whatsapp_enabled" id="waEnabledHidden" value="{{ $s('whatsapp_enabled','0') }}">
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number ID</label>
                <input type="text" name="whatsapp_phone_number_id" value="{{ $s('whatsapp_phone_number_id') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono"
                    placeholder="e.g. 123456789012345">
                <p class="text-xs text-gray-400 mt-1">Found in Meta Business → WhatsApp → API Setup</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fallback Country Code</label>
                <select name="whatsapp_country_code" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach($countryCodes as $countryCode)
                        <option value="{{ $countryCode->dial_code }}" @selected($s('whatsapp_country_code','234') === $countryCode->dial_code)>
                            +{{ $countryCode->dial_code }} ({{ $countryCode->country_name }})
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">Used only when a number has no linked country selection.</p>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Permanent Access Token</label>
                <input type="password" name="whatsapp_access_token" value="{{ $s('whatsapp_access_token') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono"
                    placeholder="EAAxxxxxxxxxxxxxxxx"
                    autocomplete="off">
                <p class="text-xs text-gray-400 mt-1">Use a System User token with <code>whatsapp_business_messaging</code> permission.</p>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                Save WhatsApp Settings
            </button>
        </div>
    </form>
</div>

{{-- Tab switching script --}}
<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
        const active = b.dataset.tab === tab;
        b.classList.toggle('border-indigo-600', active);
        b.classList.toggle('text-indigo-700', active);
        b.classList.toggle('border-transparent', !active);
        b.classList.toggle('text-gray-500', !active);
    });
    document.getElementById('tab-' + tab)?.classList.remove('hidden');
    history.replaceState(null, '', '?tab=' + tab);
}

function previewLogo(input) {
    const file = input.files[0];
    document.getElementById('logoFileName').textContent = file ? file.name : 'No file chosen';
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('logoPreview');
            const placeholder = document.getElementById('logoPlaceholder');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
}

// Sync color picker <-> text input
document.querySelectorAll('input[type=color]').forEach(picker => {
    const textId = picker.name + '_text';
    const text = document.getElementById(textId);
    if (text) picker.addEventListener('input', () => text.value = picker.value);
});
// WhatsApp toggle
const waToggleBg    = document.getElementById('waToggleBg');
const waToggleKnob  = document.getElementById('waToggleKnob');
const waHidden      = document.getElementById('waEnabledHidden');
if (waToggleBg) {
    waToggleBg.addEventListener('click', () => {
        const on = waHidden.value === '1';
        waHidden.value = on ? '0' : '1';
        waToggleBg.classList.toggle('bg-green-500', !on);
        waToggleBg.classList.toggle('bg-gray-200', on);
        waToggleKnob.classList.toggle('translate-x-6', !on);
        waToggleKnob.classList.toggle('translate-x-1', on);
    });
}
</script>
@endsection
