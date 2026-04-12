<?php

namespace App\Jobs;

use App\Models\Automation;
use App\Services\Actions\GrantAccessAction;
use App\Services\Actions\RevokeAccessAction;
use App\Services\Actions\SendEmailAction;
use App\Services\Actions\CreateUserAction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExecuteAutomationAction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Automation $automation,
        public array $data
    ) {}

    public function handle(): void
    {
        try {
            Log::info("Executing Automation #{$this->automation->id}: {$this->automation->action}");

            match($this->automation->action) {
                'grant_access'  => GrantAccessAction::execute($this->automation, $this->data),
                'revoke_access' => RevokeAccessAction::execute($this->automation, $this->data),
                'send_email'    => SendEmailAction::execute($this->automation, $this->data),
                'create_user'   => CreateUserAction::execute($this->automation, $this->data),
                default         => Log::warning("Unknown action: {$this->automation->action}"),
            };

        } catch (\Exception $e) {
            Log::error("ExecuteAutomationAction failed for automation #{$this->automation->id}: " . $e->getMessage());
            throw $e;
        }
    }
}
