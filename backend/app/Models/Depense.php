<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Depense extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'exercice_id',
        'titre',
        'montant',
        'date_depense',
        'categorie',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_depense' => 'date',
    ];

    public function exercice(): BelongsTo
    {
        return $this->belongsTo(ExerciceComptable::class, 'exercice_id');
    }
}
