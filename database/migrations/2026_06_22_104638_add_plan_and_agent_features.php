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
        // 1. Add payment frequency to committees
        Schema::table('committees', function (Blueprint $table) {
            $table->enum('payment_frequency', ['daily', 'weekly', 'monthly'])->default('monthly')->after('duration');
        });

        // 2. Create pivot table for users joining committees
        Schema::create('committee_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('joined_at')->useCurrent();
            $table->enum('status', ['active', 'completed', 'defaulted'])->default('active');
            $table->timestamps();
            $table->unique(['committee_id', 'user_id']); // Ensure a user can only join once
        });

        // 3. Add collected_by to installments
        Schema::table('installments', function (Blueprint $table) {
            $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installments', function (Blueprint $table) {
            $table->dropForeign(['collected_by']);
            $table->dropColumn('collected_by');
        });

        Schema::dropIfExists('committee_user');

        Schema::table('committees', function (Blueprint $table) {
            $table->dropColumn('payment_frequency');
        });
    }
};
