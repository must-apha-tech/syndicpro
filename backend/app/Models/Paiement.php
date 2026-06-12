<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Paiement extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'appel_id',
        'user_id',
        'amount',
        'date_paiement',
        'mode',
        'reference',
        'recu_pdf_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date_paiement' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function appel(): BelongsTo
    {
        return $this->belongsTo(AppelDeFonds::class, 'appel_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Methods
     */
    public function generateReceipt(): string
    {
        // Placeholder for PDF generation logic
        // Return path to generated PDF
        return "receipts/payment_{$this->id}.pdf";
    }
}
