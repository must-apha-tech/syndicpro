<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExerciceComptable extends Model
{
    use HasFactory, HasTenant, SoftDeletes;

    protected $table = 'exercices_comptables';

    protected $fillable = [
        'residence_id',
        'annee',
        'budget_total',
        'statut',
    ];

    protected $casts = [
        'annee' => 'integer',
        'budget_total' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function residence(): BelongsTo
    {
        return $this->belongsTo(Residence::class);
    }

    public function appels(): HasMany
    {
        return $this->hasMany(AppelDeFonds::class, 'exercice_id');
    }

    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class, 'exercice_id');
    }
}
