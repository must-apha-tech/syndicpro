<?php

namespace Database\Factories;

use App\Models\AppelDeFonds;
use App\Models\ExerciceComptable;
use App\Models\Lot;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppelDeFondsFactory extends Factory
{
    protected $model = AppelDeFonds::class;

    public function definition(): array
    {
        $amount    = fake()->randomFloat(2, 500, 8000);
        $statut    = fake()->randomElement(['emis', 'partiel', 'paye']);
        $reliquat  = match ($statut) {
            'paye'    => 0,
            'partiel' => round($amount * fake()->randomFloat(2, 0.1, 0.9), 2),
            default   => $amount,
        };
        $emission  = fake()->dateTimeBetween('-12 months', '-1 month');

        return [
            'exercice_id'   => ExerciceComptable::factory(),
            'lot_id'        => Lot::factory(),
            'numero'        => 'ADF-' . fake()->unique()->numerify('####'),
            'amount'        => $amount,
            'date_emission' => $emission,
            'date_echeance' => fake()->dateTimeBetween($emission, '+3 months'),
            'statut'        => $statut,
            'reliquat'      => $reliquat,
            'tenant_id'     => null,
        ];
    }

    public function emis(): static
    {
        return $this->state(fn (array $a) => [
            'statut'   => 'emis',
            'reliquat' => $a['amount'],
        ]);
    }

    public function paye(): static
    {
        return $this->state(fn () => ['statut' => 'paye', 'reliquat' => 0]);
    }
}
