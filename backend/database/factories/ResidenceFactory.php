<?php

namespace Database\Factories;

use App\Models\Residence;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResidenceFactory extends Factory
{
    protected $model = Residence::class;

    // Pool of realistic Moroccan residence names
    private static array $names = [
        'Résidence Atlas', 'Résidence Al Farah', 'Résidence Anfa',
        'Résidence Oulad Saleh', 'Résidence Palmier', 'Résidence Habous',
        'Résidence Ain Sebaa', 'Résidence Sidi Maarouf', 'Résidence Bernoussi',
        'Résidence Hassan II', 'Résidence Ryad', 'Résidence Bouskoura',
        'Résidence Tilila', 'Résidence Aroua', 'Résidence Sahara',
        'Résidence Agdal', 'Résidence Massira', 'Résidence Souissi',
        'Résidence Guéliz', 'Résidence Targa',
    ];

    private static array $cities = [
        'Casablanca', 'Rabat', 'Marrakech', 'Fès', 'Tanger',
        'Agadir', 'Meknès', 'Oujda', 'Kénitra', 'Tétouan',
    ];

    public function definition(): array
    {
        $city = fake()->randomElement(self::$cities);

        return [
            'name'      => fake()->unique()->randomElement(self::$names),
            'address'   => fake()->buildingNumber() . ', Rue ' . fake()->streetName() . ', ' . $city,
            'city'      => $city,
            'zip_code'  => fake()->numerify('#####'),
            'nb_lots'   => fake()->numberBetween(8, 60),
            'syndic_id' => User::where('id', 1)->value('id') ?? 1,
            'phone'     => '+212 ' . fake()->numerify('6## ### ###'),
            'email'     => 'contact@' . strtolower(str_replace(' ', '', fake()->word())) . '.ma',
            'tenant_id' => null,
        ];
    }
}
