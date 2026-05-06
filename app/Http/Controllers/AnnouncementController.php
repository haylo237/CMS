<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Ministry;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::with(['publishedBy', 'branch', 'department', 'ministry'])
                                     ->latest()
                                     ->paginate(15);
        return view('announcements.index', compact('announcements'));
    }

    public function create(): View
    {
        $branches    = Branch::all();
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

        Announcement::create($data);
        return redirect()->route('announcements.index')->with('success', 'Announcement published.');
    }

    public function show(Announcement $announcement): View
    {
        $announcement->load(['publishedBy', 'branch', 'department', 'ministry']);
        return view('announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement): View
    {
        $branches    = Branch::all();
        $departments = Department::all();
        $ministries  = Ministry::all();
        return view('announcements.edit', compact('announcement', 'branches', 'departments', 'ministries'));
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
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

        $announcement->update($data);
        return redirect()->route('announcements.index')->with('success', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();
        return redirect()->route('announcements.index')->with('success', 'Announcement deleted.');
    }
}
