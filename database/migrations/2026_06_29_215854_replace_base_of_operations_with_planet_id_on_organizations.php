<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->foreignId('base_of_operations_planet_id')->nullable()->constrained('planets')->nullOnDelete();
            $table->dropColumn('base_of_operations');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['base_of_operations_planet_id']);
            $table->dropColumn('base_of_operations_planet_id');
            $table->string('base_of_operations')->nullable();
        });
    }
};
