<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Residence extends Model
{
    use HasFactory, HasTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'address',
        'city',
        'zip_code',
        'nb_lots',
        'syndic_id',
        'phone',
        'email',
    ];

    /**
     * Relationships
     */
    public function syndic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'syndic_id');
    }

    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class);
    }

    public function exercices(): HasMany
    {
        return $this->hasMany(ExerciceComptable::class);
    }

    public function assemblees(): HasMany
    {
        return $this->hasMany(Assemblee::class);
    }

    /**
     * Accessors & Getters
     */
    public function getUnpaidChargesAttribute(): float
    {
        // Sum of unpaid appels_de_fonds for this residence
        return $this->lots()->with('appels')->get()
            ->flatMap->appels
            ->whereIn('statut', ['emis', 'partiel'])
            ->sum('reliquat');
    }

    public function getTotalQuotePartsAttribute(): int
    {
        return $this->lots()->sum('quote_part');
    }
}
