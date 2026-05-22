<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'tax_id',
                'business_address',
                'employee_id',
                'hire_date',
                'specialization',
                'hourly_rate',
                'employee_status',
                'customer_id',
                'billing_address',
                'shipping_address',
                'credit_limit',
                'preferred_payment_method',
            ]);
        });
    }
};
