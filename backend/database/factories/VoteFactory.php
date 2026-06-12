<?php

namespace Database\Factories;

use App\Models\Vote;
use App\Models\Assemblee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoteFactory extends Factory
{
    protected $model = Vote::class;

    public function definition(): array
    {
        return [
            'assemblee_id' => Assemblee::factory(),
            'user_id'      => User::factory(),
            'vote'         => fake()->randomElement(['pour', 'contre', 'abstention']),
            'comment'      => fake('fr_FR')->sentence(),
            'tenant_id'    => null,
        ];
    }
}
