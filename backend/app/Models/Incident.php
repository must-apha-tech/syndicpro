<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Incident extends Model
{
    use HasFactory, HasTenant, SoftDeletes;

    protected $fillable = [
        'residence_id',
        'lot_id',
        'titre',
        'description',
        'priorite',
        'statut',
        'assignee_id',
    ];

    /**
     * Relationships
     */
    public function residence(): BelongsTo
    {
        return $this->belongsTo(Residence::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function commentaires(): HasMany
    {
        return $this->hasMany(Commentaire::class);
    }
}
