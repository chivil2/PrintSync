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
        Schema::table('technical_services', function (Blueprint $table) {
            $table->integer('production_time')->default(3)->after('price')->comment('Production time in days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('technical_services', function (Blueprint $table) {
            $table->dropColumn('production_time');
        });
    }
};
