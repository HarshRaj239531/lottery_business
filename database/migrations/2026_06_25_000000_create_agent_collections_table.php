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
        Schema::create('agent_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->enum('collection_type', ['committee', 'loan']);
            $table->decimal('amount_collected', 15, 2);
            $table->string('details')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            // Temporary columns dropped in later migration
            $table->decimal('commission_percentage', 5, 2)->default(0);
            $table->decimal('commission_amount', 15, 2)->default(0);
            $table->enum('commission_status', ['pending', 'paid'])->default('pending');
            
            $table->timestamp('collected_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_collections');
    }
};
