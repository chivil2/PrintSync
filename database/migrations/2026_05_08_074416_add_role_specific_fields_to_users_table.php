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
            $table->string('company_name')->nullable()->after('email');
            $table->string('tax_id')->nullable()->after('company_name');
            $table->text('business_address')->nullable()->after('tax_id');
            $table->string('employee_id')->nullable()->after('business_address');
            $table->date('hire_date')->nullable()->after('employee_id');
            $table->string('specialization')->nullable()->after('hire_date');
            $table->decimal('hourly_rate', 10, 2)->nullable()->after('specialization');
            $table->string('employee_status')->nullable()->after('hourly_rate');
            $table->foreignId('customer_id')->nullable()->after('employee_status')->constrained()->nullOnDelete();
            $table->text('billing_address')->nullable()->after('customer_id');
            $table->text('shipping_address')->nullable()->after('billing_address');
            $table->decimal('credit_limit', 10, 2)->nullable()->after('shipping_address');
            $table->string('preferred_payment_method')->nullable()->after('credit_limit');
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
