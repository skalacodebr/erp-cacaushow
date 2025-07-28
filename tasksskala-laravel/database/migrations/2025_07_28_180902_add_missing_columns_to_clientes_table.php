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
        // First, let's just add the missing columns to the existing table
        Schema::table('clientes', function (Blueprint $table) {
            // Remove columns that shouldn't be there
            if (Schema::hasColumn('clientes', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
            if (Schema::hasColumn('clientes', 'password')) {
                $table->dropColumn('password');
            }
            if (Schema::hasColumn('clientes', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
            
            // Add missing columns
            if (!Schema::hasColumn('clientes', 'nome_fantasia')) {
                $table->string('nome_fantasia')->nullable()->after('nome');
            }
            if (!Schema::hasColumn('clientes', 'tipo_pessoa')) {
                $table->enum('tipo_pessoa', ['fisica', 'juridica'])->default('fisica')->after('nome_fantasia');
            }
            if (!Schema::hasColumn('clientes', 'cpf_cnpj')) {
                $table->string('cpf_cnpj')->nullable()->after('tipo_pessoa');
            }
            if (!Schema::hasColumn('clientes', 'rg_ie')) {
                $table->string('rg_ie')->nullable()->after('cpf_cnpj');
            }
            if (!Schema::hasColumn('clientes', 'telefone')) {
                $table->string('telefone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('clientes', 'celular')) {
                $table->string('celular')->nullable()->after('telefone');
            }
            if (!Schema::hasColumn('clientes', 'website')) {
                $table->string('website')->nullable()->after('celular');
            }
            if (!Schema::hasColumn('clientes', 'cep')) {
                $table->string('cep')->nullable()->after('website');
            }
            if (!Schema::hasColumn('clientes', 'endereco')) {
                $table->string('endereco')->nullable()->after('cep');
            }
            if (!Schema::hasColumn('clientes', 'numero')) {
                $table->string('numero')->nullable()->after('endereco');
            }
            if (!Schema::hasColumn('clientes', 'complemento')) {
                $table->string('complemento')->nullable()->after('numero');
            }
            if (!Schema::hasColumn('clientes', 'bairro')) {
                $table->string('bairro')->nullable()->after('complemento');
            }
            if (!Schema::hasColumn('clientes', 'cidade')) {
                $table->string('cidade')->nullable()->after('bairro');
            }
            if (!Schema::hasColumn('clientes', 'estado')) {
                $table->string('estado', 2)->nullable()->after('cidade');
            }
            if (!Schema::hasColumn('clientes', 'limite_credito')) {
                $table->decimal('limite_credito', 10, 2)->default(0)->after('estado');
            }
            if (!Schema::hasColumn('clientes', 'prazo_pagamento')) {
                $table->integer('prazo_pagamento')->default(30)->after('limite_credito');
            }
            if (!Schema::hasColumn('clientes', 'data_cadastro')) {
                $table->date('data_cadastro')->default(now())->after('prazo_pagamento');
            }
            if (!Schema::hasColumn('clientes', 'data_ultima_compra')) {
                $table->date('data_ultima_compra')->nullable()->after('data_cadastro');
            }
            if (!Schema::hasColumn('clientes', 'observacoes')) {
                $table->text('observacoes')->nullable()->after('ativo');
            }
        });
        
        // Add unique index to cpf_cnpj
        Schema::table('clientes', function (Blueprint $table) {
            $table->unique('cpf_cnpj');
            $table->index('nome');
            $table->index('tipo_pessoa');
            $table->index('ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration recreates the table, so we can't reverse it
        // The down method would need to restore the previous incorrect structure
    }
};
