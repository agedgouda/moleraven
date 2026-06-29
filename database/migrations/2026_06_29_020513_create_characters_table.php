<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_current')->default(false);
            $table->string('status')->default('active');
            $table->unsignedTinyInteger('strength')->default(7);
            $table->unsignedTinyInteger('dexterity')->default(7);
            $table->unsignedTinyInteger('endurance')->default(7);
            $table->unsignedTinyInteger('intelligence')->default(7);
            $table->unsignedTinyInteger('education')->default(7);
            $table->unsignedTinyInteger('social_standing')->default(7);
            $table->unsignedTinyInteger('age')->default(18);
            $table->string('homeworld')->nullable();
            $table->unsignedBigInteger('credits')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
