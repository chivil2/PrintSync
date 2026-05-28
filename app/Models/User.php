<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'first_name',
    'last_name',
    'email',
    'phone',
    'password',
    'title',
    // Owner-specific fields
    'company_name',
    'tax_id',
    'business_address',
    // Employee-specific fields
    'employee_id',
    'hire_date',
    'specialization',
    'hourly_rate',
    'employee_status',
    // Customer-specific fields
    'customer_id',
    'billing_address',
    'shipping_address',
    'credit_limit',
    'preferred_payment_method',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's full name
     */
    public function getNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::substr($this->first_name, 0, 1).Str::substr($this->last_name, 0, 1);
    }

    public function specializationLabel(): string
    {
        return match ($this->specialization) {
            'printing_staff' => 'Printing Staff',
            'technical_staff' => 'Technical Staff',
            default => $this->specialization ?? '—',
        };
    }

    public function serviceJobs()
    {
        return $this->hasMany(ServiceJob::class, 'employee_id');
    }
}
