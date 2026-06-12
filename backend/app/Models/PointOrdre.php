<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PointOrdre extends Model
{
    use HasFactory, HasTenant;

    protected $table = 'points_ordre';

    protected $fillable = [
        'assemblee_id',
        'titre',
        'description',
        'resultat',
    ];

    public function assemblee(): BelongsTo
    {
        return $this->belongsTo(Assemblee::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}
