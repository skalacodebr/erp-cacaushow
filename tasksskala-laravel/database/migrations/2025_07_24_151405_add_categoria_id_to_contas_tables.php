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
        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->dropColumn('categoria');
            $table->foreignId('categoria_id')->nullable()->after('conta_bancaria_id')->constrained('categorias_financeiras');
        });
        
        Schema::table('contas_receber', function (Blueprint $table) {
            $table->dropColumn('categoria');
            $table->foreignId('categoria_id')->nullable()->after('cliente_nome')->constrained('categorias_financeiras');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropColumn('categoria_id');
            $table->string('categoria')->nullable();
        });
        
        Schema::table('contas_receber', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropColumn('categoria_id');
            $table->string('categoria')->nullable();
        });
    }
};
