<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    private const TEMPLATE_TYPES = ['report', 'certificate', 'letter', 'receipt', 'other'];

    public function index(): View
    {
        $this->authorize('manage-settings');

        $groups    = ['general', 'branding', 'finance', 'notifications'];
        $settings  = Setting::all()->groupBy('group');
        $templates = DocumentTemplate::with('uploadedBy')->latest()->get()->groupBy('type');

        return view('settings.index', compact('settings', 'templates', 'groups'));
    }

    // ─── General / Finance / Notifications tabs ───────────────────────

    public function updateGeneral(Request $request): RedirectResponse
    {
        $this->authorize('manage-settings');

        $keys = [
            'church_name', 'church_address', 'church_city',
            'church_phone', 'church_email', 'church_website', 'church_motto',
            'currency_symbol', 'timezone',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key));
            }
        }

        return back()->with('success', 'General settings saved.')->with('tab', 'general');
    }

    public function updateBranding(Request $request): RedirectResponse
    {
        $this->authorize('manage-settings');

        $request->validate([
            'church_logo'     => 'nullable|image|max:2048',
            'primary_color'   => 'nullable|string|max:20',
            'report_header_bg'=> 'nullable|string|max:20',
        ]);

        if ($request->hasFile('church_logo')) {
            // Delete old logo file if present
            $old = Setting::get('church_logo');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            $path = $request->file('church_logo')->store('branding', 'public');
            Setting::set('church_logo', $path);
        }

        foreach (['primary_color', 'report_header_bg'] as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key));
            }
        }

        return back()->with('success', 'Branding settings saved.')->with('tab', 'branding');
    }

    public function updateFinance(Request $request): RedirectResponse
    {
        $this->authorize('manage-settings');

        foreach (['fiscal_year_start', 'finance_approval'] as $key) {
            Setting::set($key, $request->input($key, '0'));
        }

        return back()->with('success', 'Finance settings saved.')->with('tab', 'finance');
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $this->authorize('manage-settings');

        foreach (['notify_new_member', 'notify_new_report'] as $key) {
            Setting::set($key, $request->has($key) ? '1' : '0');
        }

        return back()->with('success', 'Notification settings saved.')->with('tab', 'notifications');
    }

    // ─── Document Templates ───────────────────────────────────────────

    public function storeTemplate(Request $request): RedirectResponse
    {
        $this->authorize('manage-settings');

        $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:' . implode(',', self::TEMPLATE_TYPES),
            'file'        => 'required|file|mimes:pdf,doc,docx,odt,xlsx,xls,ods,pptx,png,jpg|max:10240',
            'description' => 'nullable|string',
            'is_default'  => 'nullable|boolean',
        ]);

        $file         = $request->file('file');
        $path         = $file->store('templates', 'public');

        if ($request->boolean('is_default')) {
            DocumentTemplate::where('type', $request->type)->update(['is_default' => false]);
        }

        DocumentTemplate::create([
            'name'          => $request->name,
            'type'          => $request->type,
            'file_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'file_size'     => $file->getSize(),
            'description'   => $request->description,
            'uploaded_by'   => auth()->user()->member_id,
            'is_default'    => $request->boolean('is_default'),
        ]);

        return back()->with('success', 'Template uploaded successfully.')->with('tab', 'templates');
    }

    public function destroyTemplate(DocumentTemplate $template): RedirectResponse
    {
        $this->authorize('manage-settings');

        if (Storage::disk('public')->exists($template->file_path)) {
            Storage::disk('public')->delete($template->file_path);
        }
        $template->delete();

        return back()->with('success', 'Template deleted.')->with('tab', 'templates');
    }

    public function setDefaultTemplate(DocumentTemplate $template): RedirectResponse
    {
        $this->authorize('manage-settings');

        DocumentTemplate::where('type', $template->type)->update(['is_default' => false]);
        $template->update(['is_default' => true]);

        return back()->with('success', 'Default template updated.')->with('tab', 'templates');
    }
}
