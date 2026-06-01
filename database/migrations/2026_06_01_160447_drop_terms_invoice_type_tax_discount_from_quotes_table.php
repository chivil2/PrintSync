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
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['terms', 'invoice_type', 'tax', 'discount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->text('terms')->nullable()->after('notes');
            $table->string('invoice_type')->default('digital')->after('payment_status');
            $table->decimal('tax', 10, 2)->default(0)->after('subtotal');
            $table->decimal('discount', 10, 2)->default(0)->after('tax');
        });
    }
};
