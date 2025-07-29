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
        Schema::table('invoices', function (Blueprint $table) {
            $table->text('deletion_reason')->nullable()->after('cancellation_reason');
            $table->foreignId('deleted_by')->nullable()->after('deletion_reason')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['deleted_by']);
            $table->dropColumn(['deletion_reason', 'deleted_by']);
        });
    }
};
