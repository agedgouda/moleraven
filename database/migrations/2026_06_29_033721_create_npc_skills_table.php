<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('npc_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('npc_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedTinyInteger('level')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('npc_skills');
    }
};
