<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        $memberId = auth()->user()->member_id;
        $inbox    = Message::with('sender')
                            ->inbox($memberId)
                            ->latest()
                            ->paginate(20, ['*'], 'inbox_page');
        $sent     = Message::with('recipient')
                            ->sent($memberId)
                            ->latest()
                            ->paginate(20, ['*'], 'sent_page');
        $unreadCount = Message::inbox($memberId)->whereNull('read_at')->count();
        return view('messages.index', compact('inbox', 'sent', 'unreadCount'));
    }

    public function create(): View
    {
        $members = Member::orderBy('first_name')->get();
        return view('messages.create', compact('members'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'recipient_id' => 'required|exists:members,id',
            'subject'      => 'required|string|max:255',
            'body'         => 'required|string',
        ]);

        $data['sender_id'] = auth()->user()->member_id;
        Message::create($data);
        return redirect()->route('messages.index')->with('success', 'Message sent.');
    }

    public function show(Message $message): View
    {
        $memberId = auth()->user()->member_id;
        abort_unless(
            $message->recipient_id === $memberId || $message->sender_id === $memberId,
            403
        );

        if ($message->recipient_id === $memberId && !$message->read_at) {
            $message->update(['read_at' => now()]);
        }

        $message->load(['sender', 'recipient']);
        return view('messages.show', compact('message'));
    }

    public function destroy(Message $message): RedirectResponse
    {
        $memberId = auth()->user()->member_id;
        abort_unless(
            $message->sender_id === $memberId || $message->recipient_id === $memberId,
            403
        );

        $message->delete();
        return redirect()->route('messages.index')->with('success', 'Message deleted.');
    }
}
