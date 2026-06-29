<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn(['homeworld', 'last_known_planet']);
            $table->foreignId('homeworld_planet_id')->nullable()->constrained('planets')->nullOnDelete();
            $table->foreignId('last_known_planet_id')->nullable()->constrained('planets')->nullOnDelete();
        });

        Schema::table('npcs', function (Blueprint $table) {
            $table->dropColumn(['homeworld', 'last_known_planet']);
            $table->foreignId('homeworld_planet_id')->nullable()->constrained('planets')->nullOnDelete();
            $table->foreignId('last_known_planet_id')->nullable()->constrained('planets')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropForeign(['homeworld_planet_id']);
            $table->dropForeign(['last_known_planet_id']);
            $table->dropColumn(['homeworld_planet_id', 'last_known_planet_id']);
            $table->string('homeworld')->nullable();
            $table->string('last_known_planet')->nullable();
        });

        Schema::table('npcs', function (Blueprint $table) {
            $table->dropForeign(['homeworld_planet_id']);
            $table->dropForeign(['last_known_planet_id']);
            $table->dropColumn(['homeworld_planet_id', 'last_known_planet_id']);
            $table->string('homeworld')->nullable();
            $table->string('last_known_planet')->nullable();
        });
    }
};
