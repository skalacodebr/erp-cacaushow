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
        // Verifica se a coluna fornecedor não existe e adiciona
        if (!Schema::hasColumn('contas_pagar', 'fornecedor')) {
            Schema::table('contas_pagar', function (Blueprint $table) {
                $table->string('fornecedor')->nullable();
            });
        }
        
        // Adiciona categoria se não existir
        if (!Schema::hasColumn('contas_pagar', 'categoria')) {
            Schema::table('contas_pagar', function (Blueprint $table) {
                $table->string('categoria')->nullable();
            });
        }
        
        // Adiciona outras colunas que podem estar faltando
        if (!Schema::hasColumn('contas_pagar', 'forma_pagamento')) {
            Schema::table('contas_pagar', function (Blueprint $table) {
                $table->string('forma_pagamento')->nullable()->after('status');
            });
        }
        
        if (!Schema::hasColumn('contas_pagar', 'numero_documento')) {
            Schema::table('contas_pagar', function (Blueprint $table) {
                $table->string('numero_documento')->nullable()->after('forma_pagamento');
            });
        }
        
        // Fazer o mesmo para contas_receber
        if (!Schema::hasColumn('contas_receber', 'cliente_nome')) {
            Schema::table('contas_receber', function (Blueprint $table) {
                $table->string('cliente_nome')->nullable()->after('cliente_id');
            });
        }
        
        if (!Schema::hasColumn('contas_receber', 'forma_recebimento')) {
            Schema::table('contas_receber', function (Blueprint $table) {
                $table->string('forma_recebimento')->nullable()->after('status');
            });
        }
        
        if (!Schema::hasColumn('contas_receber', 'numero_documento')) {
            Schema::table('contas_receber', function (Blueprint $table) {
                $table->string('numero_documento')->nullable()->after('forma_recebimento');
            });
        }
        
        if (!Schema::hasColumn('contas_receber', 'data_recebimento')) {
            Schema::table('contas_receber', function (Blueprint $table) {
                $table->date('data_recebimento')->nullable()->after('data_vencimento');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->dropColumn(['fornecedor', 'forma_pagamento', 'numero_documento']);
        });
        
        Schema::table('contas_receber', function (Blueprint $table) {
            $table->dropColumn(['cliente_nome', 'forma_recebimento', 'numero_documento', 'data_recebimento']);
        });
    }
};