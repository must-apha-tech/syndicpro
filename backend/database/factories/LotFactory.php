<?php

namespace Database\Factories;

use App\Models\Lot;
use App\Models\Residence;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LotFactory extends Factory
{
    protected $model = Lot::class;

    public function definition(): array
    {
        $types = ['appartement', 'studio', 'local_commercial', 'parking', 'bureau'];
        $floors = ['RDC', '1er', '2ème', '3ème', '4ème', '5ème'];

        return [
            'residence_id'   => Residence::factory(),
            'proprietaire_id'=> User::factory(),
            'numero'         => strtoupper(fake()->randomLetter()) . fake()->numberBetween(1, 30),
            'etage'          => fake()->randomElement($floors),
            'type'           => fake()->randomElement($types),
            'superficie'     => fake()->numberBetween(30, 200),
            'quote_part'     => fake()->numberBetween(50, 1000),
            'tenant_id'      => null,
        ];
    }
}
