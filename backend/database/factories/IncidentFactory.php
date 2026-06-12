<?php

namespace Database\Factories;

use App\Models\Incident;
use App\Models\Residence;
use App\Models\Lot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncidentFactory extends Factory
{
    protected $model = Incident::class;

    private static array $titres = [
        'Fuite d\'eau dans le couloir',
        'Panne d\'ascenseur',
        'Problème d\'éclairage parking',
        'Porte d\'entrée défectueuse',
        'Infiltration toiture',
        'Bruit excessif appartement voisin',
        'Poubelles non collectées',
        'Dégâts des eaux cave',
        'Graffiti escalier commun',
        'Interphone en panne',
        'Fissure mur façade',
        'Chaufferie hors service',
        'Inondation local technique',
        'Vitrage cassé hall d\'entrée',
        'Antenne collective défaillante',
    ];

    public function definition(): array
    {
        return [
            'residence_id' => Residence::factory(),
            'lot_id'       => null,
            'titre'        => fake()->randomElement(self::$titres),
            'description'  => fake('fr_FR')->paragraph(3),
            'priorite'     => fake()->randomElement(['basse', 'moyenne', 'haute', 'critique']),
            'statut'       => fake()->randomElement(['nouveau', 'en_cours', 'en_attente_prestataire', 'resolu', 'clos']),
            'assignee_id'  => null,
            'tenant_id'    => null,
        ];
    }

    public function nouveau(): static
    {
        return $this->state(fn () => ['statut' => 'nouveau', 'priorite' => 'haute']);
    }

    public function resolu(): static
    {
        return $this->state(fn () => ['statut' => 'resolu']);
    }
}
