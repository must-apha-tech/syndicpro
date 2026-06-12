<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vote extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'point_ordre_id',
        'proprietaire_id',
        'decision',
        'procuration_id',
        'voted_at',
    ];

    protected $casts = [
        'voted_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function pointOrdre(): BelongsTo
    {
        return $this->belongsTo(PointOrdre::class);
    }

    public function proprietaire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proprietaire_id');
    }
}
