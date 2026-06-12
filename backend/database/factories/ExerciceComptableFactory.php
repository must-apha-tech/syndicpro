<?php

namespace Database\Factories;

use App\Models\ExerciceComptable;
use App\Models\Residence;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExerciceComptableFactory extends Factory
{
    protected $model = ExerciceComptable::class;

    public function definition(): array
    {
        $annee = fake()->numberBetween(2022, 2026);

        return [
            'residence_id' => Residence::factory(),
            'annee'        => $annee,
            'budget_total' => fake()->numberBetween(50000, 500000),
            'statut'       => $annee < 2026 ? 'clos' : 'ouvert',
            'tenant_id'    => null,
        ];
    }

    public function ouvert(): static
    {
        return $this->state(fn () => ['annee' => 2026, 'statut' => 'ouvert']);
    }

    public function clos(): static
    {
        return $this->state(fn () => ['statut' => 'clos']);
    }
}
