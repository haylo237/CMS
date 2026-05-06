<?php

namespace App\Jobs;

use App\Models\Member;
use App\Models\WhatsAppSendLog;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppAnnouncement implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 1;
    public int $timeout = 300;

    public function __construct(
        public readonly int    $logId,
        public readonly string $message,
        public readonly array  $memberIds,
    ) {}

    public function handle(WhatsAppService $whatsApp): void
    {
        $log = WhatsAppSendLog::find($this->logId);
        if (!$log) {
            return;
        }

        $log->update(['status' => 'sending']);

        $sent   = 0;
        $failed = 0;

        // Process in chunks to avoid memory spikes
        foreach (array_chunk($this->memberIds, 50) as $chunk) {
            $members = Member::whereIn('id', $chunk)
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->get();

            foreach ($members as $member) {
                $success = $whatsApp->sendMessage($member->phone, $this->message);
                $success ? $sent++ : $failed++;
            }
        }

        $log->update([
            'sent_count'   => $sent,
            'failed_count' => $failed,
            'status'       => 'completed',
        ]);
    }

    public function failed(\Throwable $e): void
    {
        WhatsAppSendLog::where('id', $this->logId)->update([
            'status'        => 'failed',
            'error_message' => $e->getMessage(),
        ]);
    }
}
