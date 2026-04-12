<?php

namespace App\Services\Actions;

use App\Models\Automation;
use App\Models\Enrollment;
use App\Models\User;

class RevokeAccessAction
{
    public static function execute(Automation $automation, array $data): void
    {
        $email = $data['buyer_email'] ?? '';
        if (!$email || !$automation->product_id) return;

        $user = User::where('email', $email)->first();
        if (!$user) return;

        Enrollment::where('user_id', $user->id)
            ->where('product_id', $automation->product_id)
            ->update(['status' => 'cancelled']);
    }
}
