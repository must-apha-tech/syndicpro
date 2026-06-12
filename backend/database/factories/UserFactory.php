<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name'              => fake('fr_FR')->name(),
            'email'             => fake()->unique()->safeEmail(),
            'password'          => Hash::make('password'),
            'phone'             => fake('fr_FR')->phoneNumber(),
            'tenant_id'         => null,
            'is_active'         => true,
            'email_verified_at' => now(),
        ];
    }

    public function proprietaire(): static
    {
        return $this->state(fn () => ['tenant_id' => null]);
    }
}
