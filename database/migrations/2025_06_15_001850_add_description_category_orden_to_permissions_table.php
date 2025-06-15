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
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name')->comment('Descripción del permiso');
            $table->string('category')->nullable()->after('description')->comment('Categoría del permiso');
            $table->integer('orden')->nullable()->after('category')->comment('Orden para mostrar en UI');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['description', 'category', 'orden']);
        });
    }
};
