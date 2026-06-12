<?php

namespace Database\Factories;

use App\Models\Paiement;
use App\Models\AppelDeFonds;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaiementFactory extends Factory
{
    protected $model = Paiement::class;

    public function definition(): array
    {
        return [
            'appel_id'      => AppelDeFonds::factory(),
            'user_id'       => User::factory(),
            'amount'        => fake()->randomFloat(2, 200, 8000),
            'date_paiement' => fake()->dateTimeBetween('-6 months', 'now'),
            'mode'          => fake()->randomElement(['virement', 'cheque', 'especes', 'en_ligne']),
            'reference'     => strtoupper(fake()->bothify('??-####-??')),
            'tenant_id'     => null,
        ];
    }
}
