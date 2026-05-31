<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'sku', 'description', 'quantity', 'min_stock_level', 'unit_price', 'unit', 'supplier', 'location', 'status'])]
class Inventory extends Model
{
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'min_stock_level' => 'integer',
            'unit_price' => 'decimal:2',
        ];
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->min_stock_level;
    }

    public function isOutOfStock(): bool
    {
        return $this->quantity === 0;
    }

    public function updateStatus(): void
    {
        if ($this->isOutOfStock()) {
            $this->status = 'out_of_stock';
        } elseif ($this->isLowStock()) {
            $this->status = 'low_stock';
        } else {
            $this->status = 'in_stock';
        }
        $this->save();
    }
}
