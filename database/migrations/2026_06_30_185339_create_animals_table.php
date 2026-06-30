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
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('native_planet_id')->nullable()->constrained('planets')->nullOnDelete();
            $table->foreignId('parent_animal_id')->nullable()->constrained('animals')->nullOnDelete();
            $table->unsignedSmallInteger('hits')->nullable();
            $table->unsignedSmallInteger('speed')->nullable();
            $table->string('behavior_type')->nullable();
            $table->string('behavior_subtype')->nullable();
            $table->text('notes')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
