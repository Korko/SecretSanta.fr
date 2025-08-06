<?php

namespace Database\Factories\User;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        $email = fake()->unique()->safeEmail();
        
        return [
            'email_hash' => hash('sha256', $email),
            'email_blind_index' => hash_hmac('sha256', $email, config('app.key')),
            'email_encrypted' => encrypt($email),
            'password' => bcrypt('password'),
            'last_login_at' => null,
        ];
    }
}
