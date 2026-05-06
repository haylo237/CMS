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
        $groups    = ['general', 'branding', 'finance', 'notifications'];
        $settings  = Setting::all()->groupBy('group');
        $templates = DocumentTemplate::with('uploadedBy')->latest()->get()->groupBy('type');

        return view('settings.index', compact('settings', 'templates', 'groups'));
    }

    // ─── General / Finance / Notifications tabs ───────────────────────

    public function updateGeneral(Request $request): RedirectResponse
    {

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

        foreach (['fiscal_year_start', 'finance_approval'] as $key) {
            Setting::set($key, $request->input($key, '0'));
        }

        return back()->with('success', 'Finance settings saved.')->with('tab', 'finance');
    }

    public function updateNotifications(Request $request): RedirectResponse
    {

        foreach (['notify_new_member', 'notify_new_report'] as $key) {
            Setting::set($key, $request->has($key) ? '1' : '0');
        }

        return back()->with('success', 'Notification settings saved.')->with('tab', 'notifications');
    }

    // ─── WhatsApp ─────────────────────────────────────────────────────

    public function updateWhatsApp(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'whatsapp_enabled'         => 'nullable|in:0,1',
            'whatsapp_phone_number_id' => 'nullable|string|max:100',
            'whatsapp_access_token'    => 'nullable|string|max:500',
            'whatsapp_country_code'    => 'nullable|string|max:10',
        ]);

        Setting::set('whatsapp_enabled',         $data['whatsapp_enabled'] ?? '0');
        Setting::set('whatsapp_phone_number_id', $data['whatsapp_phone_number_id'] ?? '');
        Setting::set('whatsapp_country_code',    $data['whatsapp_country_code'] ?? '234');

        // Only overwrite access token if a new value was provided
        if (!empty($data['whatsapp_access_token'])) {
            Setting::set('whatsapp_access_token', $data['whatsapp_access_token']);
        }

        return back()->with('success', 'WhatsApp settings saved.')->with('tab', 'whatsapp');
    }

    // ─── Document Templates ───────────────────────────────────────────

    public function storeTemplate(Request $request): RedirectResponse
    {

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

        if (Storage::disk('public')->exists($template->file_path)) {
            Storage::disk('public')->delete($template->file_path);
        }
        $template->delete();

        return back()->with('success', 'Template deleted.')->with('tab', 'templates');
    }

    public function setDefaultTemplate(DocumentTemplate $template): RedirectResponse
    {

        DocumentTemplate::where('type', $template->type)->update(['is_default' => false]);
        $template->update(['is_default' => true]);

        return back()->with('success', 'Default template updated.')->with('tab', 'templates');
    }
}
