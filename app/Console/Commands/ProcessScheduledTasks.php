<?php

namespace App\Console\Commands;

use App\Jobs\ExecuteAutomationAction;
use App\Models\ScheduledTask;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessScheduledTasks extends Command
{
    protected $signature = 'automations:process';
    protected $description = 'Process due scheduled automation tasks';

    public function handle(): int
    {
        $tasks = ScheduledTask::due()->with('automation')->get();

        if ($tasks->isEmpty()) {
            $this->line('No due tasks to process.');
            return self::SUCCESS;
        }

        $this->info("Processing {$tasks->count()} due task(s)...");

        $processed = 0;
        $failed    = 0;

        foreach ($tasks as $task) {
            // Guard: automation may have been deleted between scheduling and execution
            if (!$task->automation) {
                $task->update(['status' => 'cancelled', 'error_message' => 'Automation was deleted.']);
                $this->warn("Task #{$task->id} cancelled — automation no longer exists.");
                continue;
            }

            try {
                $this->line("  → Task #{$task->id} | {$task->automation->name} | {$task->user_email}");

                ExecuteAutomationAction::dispatchSync($task->automation, $task->payload);

                $task->update(['status' => 'processed']);
                $processed++;

                Log::info("automations:process — Task #{$task->id} processed successfully.", [
                    'automation_id' => $task->automation_id,
                    'user_email'    => $task->user_email,
                ]);

            } catch (\Throwable $e) {
                $task->update([
                    'status'        => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
                $failed++;

                $this->error("  ✗ Task #{$task->id} failed: " . $e->getMessage());
                Log::error("automations:process — Task #{$task->id} failed.", [
                    'automation_id' => $task->automation_id,
                    'user_email'    => $task->user_email,
                    'error'         => $e->getMessage(),
                ]);
            }
        }

        $this->info("Done: {$processed} processed, {$failed} failed.");
        return self::SUCCESS;
    }
}
