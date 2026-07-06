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
        Schema::create('material_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('title');
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('success'); // success, pending, failed
            $table->string('type')->default('credit'); // credit, debit
            $table->timestamps();

            $table->foreign('material_id')->references('id')->on('materials')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_stocks');
    }
};
