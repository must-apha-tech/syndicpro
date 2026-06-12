<?php

namespace Database\Factories;

use App\Models\Assemblee;
use App\Models\Residence;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssembleeFactory extends Factory
{
    protected $model = Assemblee::class;

    private static array $objets = [
        'Assemblée Générale Ordinaire 2025',
        'Assemblée Générale Ordinaire 2024',
        'AG Extraordinaire - Travaux toiture',
        'AG Extraordinaire - Révision budget',
        'Assemblée de copropriété - Règlement intérieur',
        'AG Ordinaire - Élection syndic',
    ];

    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-18 months', '+3 months');
        $isPast = $date < new \DateTime();

        return [
            'residence_id' => Residence::factory(),
            'objet'        => fake()->randomElement(self::$objets),
            'date_heure'   => $date,
            'lieu'         => fake()->randomElement([
                'Salle de réunion de la résidence',
                'Hall d\'accueil - RDC',
                'Local syndic',
                'Salle polyvalente',
            ]),
            'statut'       => $isPast
                ? fake()->randomElement(['tenue', 'annulee'])
                : 'planifiee',
            'convocation_envoyee' => $isPast,
            'tenant_id'    => null,
        ];
    }

    public function planifiee(): static
    {
        return $this->state(fn () => [
            'statut'              => 'planifiee',
            'date_heure'          => fake()->dateTimeBetween('+7 days', '+60 days'),
            'convocation_envoyee' => true,
        ]);
    }
}
