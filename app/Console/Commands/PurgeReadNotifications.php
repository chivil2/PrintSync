<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('notifications:purge')]
#[Description('Delete read notifications older than 30 days')]
class PurgeReadNotifications extends Command
{
    public function handle(): void
    {
        $deleted = DB::table('notifications')
            ->whereNotNull('read_at')
            ->where('read_at', '<', now()->subDays(30))
            ->delete();

        $this->info("Purged {$deleted} read notification(s) older than 30 days.");
    }
}
