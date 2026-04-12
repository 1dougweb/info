<?php

namespace App\Services\Actions;

use App\Models\Automation;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CreateUserAction
{
    public static function execute(Automation $automation, array $data): void
    {
        $email = $data['buyer_email'] ?? '';
        if (!$email) {
            Log::warning("CreateUserAction: No email found in payload.");
            return;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name'     => $data['buyer_name'] ?? 'Membro',
                'email'    => $email,
                'phone'    => $data['buyer_phone'] ?? null,
                'password' => bcrypt($data['password'] ?? str()->random(12)),
                'role'     => 'member',
            ]);
            
            Log::info("CreateUserAction: User created for {$email}");
        } else {
            Log::info("CreateUserAction: User with email {$email} already exists.");
        }
    }
}
