<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lot extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'residence_id',
        'numero',
        'type',
        'surface',
        'quote_part',
        'proprietaire_id',
        'floor',
    ];

    protected $casts = [
        'surface' => 'decimal:2',
        'quote_part' => 'integer',
        'floor' => 'integer',
    ];

    /**
     * Relationships
     */
    public function residence(): BelongsTo
    {
        return $this->belongsTo(Residence::class);
    }

    public function proprietaire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proprietaire_id');
    }

    public function appels(): HasMany
    {
        return $this->hasMany(AppelDeFonds::class);
    }

    /**
     * Methods
     */
    public function getChargeAmountForAppel($appel): float
    {
        // Formula: appel->amount * (quote_part / 1000)
        return round($appel->amount * ($this->quote_part / 1000), 2);
    }
}
