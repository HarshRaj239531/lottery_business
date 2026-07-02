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
        Schema::table('agent_collections', function (Blueprint $table) {
            $table->dropColumn(['commission_percentage', 'commission_amount', 'commission_status']);
            $table->foreignId('installment_id')->nullable()->constrained('installments')->onDelete('cascade');
            $table->foreignId('loan_installment_id')->nullable()->constrained('loan_installments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_collections', function (Blueprint $table) {
            $table->decimal('commission_percentage', 5, 2)->default(0);
            $table->decimal('commission_amount', 15, 2)->default(0);
            $table->enum('commission_status', ['pending', 'paid'])->default('pending');
            $table->dropForeign(['installment_id']);
            $table->dropColumn('installment_id');
            $table->dropForeign(['loan_installment_id']);
            $table->dropColumn('loan_installment_id');
        });
    }
};
