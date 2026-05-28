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
            $table->foreignId('service_job_id')->nullable()->after('customer_id')->constrained('service_jobs')->onDelete('cascade');
            $table->timestamp('sent_at')->nullable()->after('updated_at');
            $table->timestamp('approved_at')->nullable()->after('sent_at');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
            $table->text('rejection_reason')->nullable()->after('rejected_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign(['service_job_id']);
            $table->dropColumn(['service_job_id', 'sent_at', 'approved_at', 'rejected_at', 'rejection_reason']);
        });
    }
};
