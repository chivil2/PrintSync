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
            $table->decimal('negotiation_adjustment', 10, 2)->nullable()->after('adjustment');
            $table->text('negotiation_notes')->nullable()->after('negotiation_adjustment');
            $table->string('negotiation_status')->nullable()->after('negotiation_notes'); // 'pending', 'accepted', 'cancelled'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['negotiation_adjustment', 'negotiation_notes', 'negotiation_status']);
        });
    }
};
