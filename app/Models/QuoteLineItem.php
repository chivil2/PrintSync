<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['quote_id', 'item_name', 'description', 'quantity', 'unit_price', 'line_total'])]
class QuoteLineItem extends Model
{
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }
}
