<?php

namespace App\Models;

use Database\Factories\ServiceJobFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['name', 'description', 'type', 'customer_id', 'employee_id', 'service_id', 'service_type', 'status', 'priority', 'started_at', 'completed_at', 'deadline', 'notes', 'technical_details', 'request_invoice', 'invoice_path'])]
class ServiceJob extends Model
{
    /** @use HasFactory<ServiceJobFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'deadline' => 'datetime',
            'technical_details' => 'array',
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

    public function service(): MorphTo
    {
        return $this->morphTo();
    }

    public function quote(): HasOne
    {
        return $this->hasOne(Quote::class);
    }
}
