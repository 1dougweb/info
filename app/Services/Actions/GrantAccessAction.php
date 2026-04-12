<?php

namespace App\Services\Actions;

use App\Models\Automation;
use App\Models\Enrollment;
use App\Models\User;

class GrantAccessAction
{
    public static function execute(Automation $automation, array $data): void
    {
        $email = $data['buyer_email'] ?? '';
        if (!$email) return;

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'     => $data['buyer_name'] ?? 'Membro',
                'phone'    => $data['buyer_phone'] ?? null,
                'password' => bcrypt($data['password'] ?? str()->random(16)),
                'role'     => 'member',
            ]
        );

        if (!$automation->product_id) return;

        Enrollment::updateOrCreate(
            ['user_id' => $user->id, 'product_id' => $automation->product_id],
            [
                'source'         => $data['source'],
                'transaction_id' => $data['transaction_id'] ?? null,
                'status'         => 'active',
            ]
        );
    }
}
