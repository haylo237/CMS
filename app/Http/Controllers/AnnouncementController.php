<?php

namespace App\Http\Controllers;

use App\Jobs\SendWhatsAppAnnouncement;
use App\Models\Announcement;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Member;
use App\Models\Ministry;
use App\Models\WhatsAppSendLog;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $branchId = auth()->user()?->isPastor() ? auth()->user()->pastoredBranchId() : null;
        $announcements = Announcement::with(['publishedBy', 'branch', 'department', 'ministry'])
                                     ->when($branchId, function ($q) use ($branchId) {
                                         $q->where(function ($inner) use ($branchId) {
                                             $inner->where('audience', 'all')->orWhere('branch_id', $branchId);
                                         });
                                     })
                                     ->latest()
                                     ->paginate(15);
        return view('announcements.index', compact('announcements'));
    }

    public function create(): View
    {
        $branchId = auth()->user()?->isPastor() ? auth()->user()->pastoredBranchId() : null;
        $branches    = $branchId ? Branch::where('id', $branchId)->get() : Branch::all();
        $departments = Department::all();
        $ministries  = Ministry::all();
        return view('announcements.create', compact('branches', 'departments', 'ministries'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'body'          => 'required|string',
            'audience'      => 'required|in:all,branch,department,ministry',
            'branch_id'     => 'nullable|exists:branches,id',
            'department_id' => 'nullable|exists:departments,id',
            'ministry_id'   => 'nullable|exists:ministries,id',
            'published_at'  => 'nullable|date',
            'expires_at'    => 'nullable|date|after:published_at',
        ]);

        $data['published_by'] = auth()->user()->member_id;
        if (empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if (auth()->user()?->isPastor()) {
            $data['audience'] = 'branch';
            $data['branch_id'] = auth()->user()->pastoredBranchId();
        }

        Announcement::create($data);
        return redirect()->route('announcements.index')->with('success', 'Announcement published.');
    }

    public function show(Announcement $announcement): View
    {
        if (auth()->user()?->isPastor() && $announcement->audience !== 'all' && $announcement->branch_id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        $announcement->load(['publishedBy', 'branch', 'department', 'ministry']);
        $sendLogs    = $announcement->sendLogs()->latest()->get();
        $branchId = auth()->user()?->isPastor() ? auth()->user()->pastoredBranchId() : null;
        $branches    = $branchId ? Branch::where('id', $branchId)->get() : Branch::all();
        $departments = Department::all();
        $ministries  = Ministry::all();
        return view('announcements.show', compact('announcement', 'sendLogs', 'branches', 'departments', 'ministries'));
    }

    public function edit(Announcement $announcement): View
    {
        if (auth()->user()?->isPastor() && $announcement->audience !== 'all' && $announcement->branch_id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        $branchId = auth()->user()?->isPastor() ? auth()->user()->pastoredBranchId() : null;
        $branches    = $branchId ? Branch::where('id', $branchId)->get() : Branch::all();
        $departments = Department::all();
        $ministries  = Ministry::all();
        return view('announcements.edit', compact('announcement', 'branches', 'departments', 'ministries'));
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        if (auth()->user()?->isPastor() && $announcement->audience !== 'all' && $announcement->branch_id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'body'          => 'required|string',
            'audience'      => 'required|in:all,branch,department,ministry',
            'branch_id'     => 'nullable|exists:branches,id',
            'department_id' => 'nullable|exists:departments,id',
            'ministry_id'   => 'nullable|exists:ministries,id',
            'published_at'  => 'nullable|date',
            'expires_at'    => 'nullable|date',
        ]);

        if (auth()->user()?->isPastor()) {
            $data['audience'] = 'branch';
            $data['branch_id'] = auth()->user()->pastoredBranchId();
        }

        $announcement->update($data);
        return redirect()->route('announcements.index')->with('success', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        if (auth()->user()?->isPastor() && $announcement->audience !== 'all' && $announcement->branch_id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        $announcement->delete();
        return redirect()->route('announcements.index')->with('success', 'Announcement deleted.');
    }

    /**
     * Dispatch a WhatsApp broadcast for this announcement.
     * Only accessible to users with the 'send-whatsapp' gate.
     */
    public function sendWhatsApp(Request $request, Announcement $announcement, WhatsAppService $whatsApp): RedirectResponse
    {
        abort_unless(Gate::allows('send-whatsapp'), 403);

        if (!$whatsApp->isConfigured()) {
            return back()->with('error', 'WhatsApp is not configured. Please update the WhatsApp settings first.');
        }

        $data = $request->validate([
            'audience_type' => 'required|in:all,branch,department,ministry',
            'audience_id'   => 'nullable|integer',
        ]);

        // Build recipient list
        $query = Member::whereNotNull('phone')
            ->where('phone', '!=', '')
            ->where('status', 'active');

        $audienceType = $data['audience_type'];
        $audienceId   = $data['audience_id'] ?? null;

        match ($audienceType) {
            'branch'     => $query->where('branch_id', $audienceId),
            'department' => $query->whereHas('departments', fn($q) => $q->where('departments.id', $audienceId)),
            'ministry'   => $query->whereHas('ministries',  fn($q) => $q->where('ministries.id', $audienceId)),
            default      => null,
        };

        $memberIds = $query->pluck('id')->toArray();
        $total     = count($memberIds);

        if ($total === 0) {
            return back()->with('error', 'No members with phone numbers found for the selected audience.');
        }

        $message = "*{$announcement->title}*\n\n{$announcement->body}";

        $log = WhatsAppSendLog::create([
            'announcement_id'  => $announcement->id,
            'sent_by'          => auth()->user()->member_id,
            'audience_type'    => $audienceType,
            'audience_id'      => $audienceId,
            'total_recipients' => $total,
            'status'           => 'pending',
        ]);

        dispatch(new SendWhatsAppAnnouncement($log->id, $message, $memberIds));

        return back()->with('success', "WhatsApp broadcast queued for {$total} member(s). You can track progress in the send log.");
    }
}
