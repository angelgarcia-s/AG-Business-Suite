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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('tipo_usuario', ['super_admin', 'empresa'])->default('empresa')->after('password');
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade')->after('tipo_usuario');
            $table->boolean('activo')->default(true)->after('empresa_id');
            $table->timestamp('ultimo_acceso')->nullable()->after('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('empresa_id');
            $table->dropColumn(['tipo_usuario', 'activo', 'ultimo_acceso']);
        });
    }
};
