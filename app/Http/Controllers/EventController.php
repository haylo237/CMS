<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $branchId = auth()->user()?->isPastor() ? auth()->user()->pastoredBranchId() : null;
        $query = Event::with(['branch', 'createdBy'])->withCount('attendances');

        if ($branchId) {
            $query->where(function ($q) use ($branchId) {
                $q->whereNull('branch_id')->orWhere('branch_id', $branchId);
            });
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('from')) {
            $query->where('date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('date', '<=', $request->to);
        }

        $events   = $query->orderByDesc('date')->paginate(15)->withQueryString();
        $branches = $branchId ? Branch::where('id', $branchId)->get() : Branch::all();
        return view('events.index', compact('events', 'branches'));
    }

    public function create(): View
    {
        $branchId = auth()->user()?->isPastor() ? auth()->user()->pastoredBranchId() : null;
        $branches = $branchId ? Branch::where('id', $branchId)->get() : Branch::all();
        $members  = Member::when($branchId, fn($q) => $q->where('branch_id', $branchId))->orderBy('first_name')->get();
        return view('events.create', compact('branches', 'members'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:service,meeting,special,prayer,outreach',
            'branch_id'   => 'nullable|exists:branches,id',
            'date'        => 'required|date',
            'time'        => 'nullable|date_format:H:i',
            'description' => 'nullable|string',
        ]);

        $data['created_by'] = auth()->user()->member_id;
        if (auth()->user()?->isPastor()) {
            $data['branch_id'] = auth()->user()->pastoredBranchId();
        }
        Event::create($data);
        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    public function show(Event $event): View
    {
        if (auth()->user()?->isPastor() && $event->branch_id && $event->branch_id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        $event->load(['branch', 'createdBy', 'attendances.member']);
        $members        = Member::when($event->branch_id, fn($q) => $q->where('branch_id', $event->branch_id))->orderBy('first_name')->get();
        $attended_ids   = $event->attendances->pluck('member_id')->toArray();
        return view('events.show', compact('event', 'members', 'attended_ids'));
    }

    public function edit(Event $event): View
    {
        if (auth()->user()?->isPastor() && $event->branch_id && $event->branch_id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        $branchId = auth()->user()?->isPastor() ? auth()->user()->pastoredBranchId() : null;
        $branches = $branchId ? Branch::where('id', $branchId)->get() : Branch::all();
        $members  = Member::when($event->branch_id ?: $branchId, fn($q, $value) => $q->where('branch_id', $value))->orderBy('first_name')->get();
        return view('events.edit', compact('event', 'branches', 'members'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        if (auth()->user()?->isPastor() && $event->branch_id && $event->branch_id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:service,meeting,special,prayer,outreach',
            'branch_id'   => 'nullable|exists:branches,id',
            'date'        => 'required|date',
            'time'        => 'nullable|date_format:H:i',
            'description' => 'nullable|string',
        ]);

        if (auth()->user()?->isPastor()) {
            $data['branch_id'] = auth()->user()->pastoredBranchId();
        }

        $event->update($data);
        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        if (auth()->user()?->isPastor() && $event->branch_id && $event->branch_id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted.');
    }

    public function markAttendance(Request $request, Event $event): RedirectResponse
    {
        if (auth()->user()?->isPastor() && $event->branch_id && $event->branch_id !== auth()->user()->pastoredBranchId()) {
            abort(403);
        }

        $request->validate([
            'attendances'          => 'nullable|array',
            'attendances.*.member_id' => 'required|exists:members,id',
            'attendances.*.status'    => 'required|in:present,absent,excused',
            'attendances.*.notes'     => 'nullable|string',
        ]);

        $recordedBy = auth()->user()->member_id;

        foreach ($request->input('attendances', []) as $row) {
            Attendance::updateOrCreate(
                ['event_id' => $event->id, 'member_id' => $row['member_id']],
                ['status' => $row['status'], 'notes' => $row['notes'] ?? null, 'recorded_by' => $recordedBy]
            );
        }

        return redirect()->route('events.show', $event)->with('success', 'Attendance saved.');
    }
}
