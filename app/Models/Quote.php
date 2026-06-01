<?php

namespace App\Models;

use Database\Factories\QuoteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['quote_number', 'customer_id', 'service_job_id', 'date', 'status', 'payment_status', 'currency', 'subtotal', 'adjustment', 'total', 'notes', 'employee_id', 'sent_at', 'approved_at', 'rejected_at', 'rejection_reason'])]
class Quote extends Model
{
    /** @use HasFactory<QuoteFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'subtotal' => 'decimal:2',
            'adjustment' => 'decimal:2',
            'total' => 'decimal:2',
            'sent_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(QuoteLineItem::class);
    }

    public function serviceJob(): BelongsTo
    {
        return $this->belongsTo(ServiceJob::class);
    }
}
