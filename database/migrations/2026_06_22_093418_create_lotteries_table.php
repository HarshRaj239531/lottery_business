<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('lotteries', function (Blueprint $table) {
        $table->id();
        $table->foreignId('committee_id')->constrained()->cascadeOnDelete();
        $table->foreignId('winner_id')->constrained('users')->cascadeOnDelete();
        $table->date('draw_date');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotteries');
    }
};
