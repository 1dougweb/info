<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessScheduledTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automations:process';

    protected $description = 'Process due scheduled automation tasks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tasks = \App\Models\ScheduledTask::due()->get();

        if ($tasks->isEmpty()) {
            return;
        }

        $this->info("Processing {$tasks->count()} due tasks...");

        foreach ($tasks as $task) {
            try {
                $this->line("Running task #{$task->id} for {$task->user_email}...");

                // Execute the action via the job (instantly, without delay now)
                \App\Jobs\ExecuteAutomationAction::dispatch($task->automation, $task->payload);

                $task->update([
                    'status' => 'processed',
                ]);

            } catch (\Exception $e) {
                $task->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
                $this->error("Task #{$task->id} failed: " . $e->getMessage());
            }
        }

        $this->info("All due tasks processed.");
    }
}
