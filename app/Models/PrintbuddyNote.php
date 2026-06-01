<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintbuddyNote extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'title',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function authorize(): bool
    {
        return $this->user_id === auth()->id();
    }
}
