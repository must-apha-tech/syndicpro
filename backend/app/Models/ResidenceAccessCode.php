<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

/**
 * Model ResidenceAccessCode
 * 
 * @property int $id
 * @property int $tenant_id
 * @property int $residence_id
 * @property int|null $lot_id
 * @property string $code
 * @property int $created_by
 * @property Carbon|null $expires_at
 * @property int|null $used_by
 * @property Carbon|null $used_at
 */
class ResidenceAccessCode extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'residence_id',
        'lot_id',
        'code',
        'created_by',
        'expires_at',
        'used_by',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    /**
     * Methods
     */

    /**
     * Check if code can be used
     */
    public function isValid(): bool
    {
        return !$this->isUsed() && !$this->isExpired();
    }

    /**
     * Check if code is expired
     */
    public function isExpired(): bool
    {
        if ($this->expires_at === null) {
            return false;
        }
        return $this->expires_at->isPast();
    }

    /**
     * Check if code has been used
     */
    public function isUsed(): bool
    {
        return $this->used_by !== null;
    }

    /**
     * Mark code as used by a specific user
     */
    public function markAsUsed(User $user): void
    {
        $this->used_by = $user->id;
        $this->used_at = now();
        $this->save();
    }

    /**
     * Scopes
     */

    /**
     * Filter valid codes
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->whereNull('used_by')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Filter by lot
     */
    public function scopeForLot(Builder $query, int $lotId): Builder
    {
        return $query->where('lot_id', $lotId);
    }

    /**
     * Filter by residence
     */
    public function scopeForResidence(Builder $query, int $residenceId): Builder
    {
        return $query->where('residence_id', $residenceId);
    }
}
