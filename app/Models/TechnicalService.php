<?php

namespace App\Models;

use Database\Factories\TechnicalServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'price', 'image', 'is_active', 'production_time'])]
class TechnicalService extends Model
{
    /** @use HasFactory<TechnicalServiceFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'production_time' => 'integer',
        ];
    }
}
