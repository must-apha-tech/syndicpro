<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class AppelDeFonds extends Model
{
    use HasFactory, HasTenant;

    protected $table = 'appels_de_fonds';

    protected $fillable = [
        'exercice_id',
        'lot_id',
        'numero',
        'amount',
        'date_emission',
        'date_echeance',
        'statut',
        'reliquat',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'reliquat' => 'decimal:2',
        'date_emission' => 'datetime',
        'date_echeance' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function exercice(): BelongsTo
    {
        return $this->belongsTo(ExerciceComptable::class, 'exercice_id');
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'appel_id');
    }

    /**
     * Accessors
     */
    public function getMontantPayeAttribute(): float
    {
        return $this->paiements()->sum('amount');
    }

    public function getMontantDuAttribute(): float
    {
        return (float) $this->amount - $this->montant_paye;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->date_echeance->isPast() && $this->statut !== 'paye';
    }
}
