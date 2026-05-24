<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['quote_number', 'customer_id', 'date', 'status', 'currency', 'subtotal', 'tax', 'discount', 'total', 'terms', 'notes', 'employee_id'])]
class Quote extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
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
}
