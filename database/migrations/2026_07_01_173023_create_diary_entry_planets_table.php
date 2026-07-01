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
        Schema::create('diary_entry_planets', function (Blueprint $table) {
            $table->foreignId('diary_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('planet_id')->constrained()->cascadeOnDelete();
            $table->primary(['diary_entry_id', 'planet_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diary_entry_planets');
    }
};
