<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assemblee extends Model
{
    use HasFactory, HasTenant, SoftDeletes;

    protected $fillable = [
        'residence_id',
        'type',
        'date_heure',
        'lieu',
        'quorum_requis',
        'statut',
        'pv_pdf_path',
    ];

    protected $casts = [
        'date_heure' => 'datetime',
        'quorum_requis' => 'integer',
    ];

    /**
     * Relationships
     */
    public function residence(): BelongsTo
    {
        return $this->belongsTo(Residence::class);
    }

    public function pointsOrdre(): HasMany
    {
        return $this->hasMany(PointOrdre::class);
    }

    public function votes(): HasMany
    {
        return $this->hasManyThrough(Vote::class, PointOrdre::class);
    }
}
