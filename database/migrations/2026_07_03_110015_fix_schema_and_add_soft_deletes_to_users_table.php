<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add SoftDeletes to users table
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        // 2. Fix installments default status & precision, add indexes
        Schema::table('installments', function (Blueprint $table) {
            // Drop enum and re-add with correct default
            // In MySQL/MariaDB modifying ENUM can be tricky with Doctrine, so we use raw DB statement
        });
        
        DB::statement("ALTER TABLE installments MODIFY COLUMN status ENUM('paid', 'pending') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE installments MODIFY COLUMN amount DECIMAL(15, 2) NOT NULL");

        Schema::table('installments', function (Blueprint $table) {
            $table->index('status');
            $table->index('due_date');
            $table->index(['user_id', 'status']);
        });

        // 3. Add indexes to agent_collections
        Schema::table('agent_collections', function (Blueprint $table) {
            $table->index('status');
            $table->index('collection_type');
            $table->index(['agent_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_collections', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['collection_type']);
            $table->dropIndex(['agent_id', 'status']);
        });

        Schema::table('installments', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['due_date']);
            $table->dropIndex(['user_id', 'status']);
        });

        DB::statement("ALTER TABLE installments MODIFY COLUMN amount DECIMAL(10, 2) NOT NULL");
        DB::statement("ALTER TABLE installments MODIFY COLUMN status ENUM('paid', 'pending') NOT NULL DEFAULT 'paid'");

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
