<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contas_receber', function (Blueprint $table) {
            // Verificar se a coluna cliente_nome não existe antes de adicionar
            if (!Schema::hasColumn('contas_receber', 'cliente_nome')) {
                $table->string('cliente_nome')->nullable()->after('conta_bancaria_id');
            }
        });
        
        // Remover cliente_id se existir
        if (Schema::hasColumn('contas_receber', 'cliente_id')) {
            // Primeiro, tentar remover a foreign key se existir
            try {
                Schema::table('contas_receber', function (Blueprint $table) {
                    $table->dropForeign(['cliente_id']);
                });
            } catch (\Exception $e) {
                // Ignorar se a foreign key não existir
            }
            
            // Depois remover a coluna
            Schema::table('contas_receber', function (Blueprint $table) {
                $table->dropColumn('cliente_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contas_receber', function (Blueprint $table) {
            if (Schema::hasColumn('contas_receber', 'cliente_nome')) {
                $table->dropColumn('cliente_nome');
            }
        });
    }
};
