<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('npcs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('strength')->default(7);
            $table->unsignedTinyInteger('dexterity')->default(7);
            $table->unsignedTinyInteger('endurance')->default(7);
            $table->unsignedTinyInteger('intelligence')->default(7);
            $table->unsignedTinyInteger('education')->default(7);
            $table->unsignedTinyInteger('social_standing')->default(7);
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('homeworld')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('npcs');
    }
};
