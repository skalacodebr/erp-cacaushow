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
        Schema::create('transacoes_ofx', function (Blueprint $table) {
            $table->id();
            $table->string('fitid')->nullable()->index();
            $table->string('tipo')->nullable();
            $table->datetime('data_transacao');
            $table->decimal('valor', 15, 2);
            $table->text('descricao')->nullable();
            $table->string('beneficiario')->nullable();
            $table->string('numero_documento')->nullable();
            $table->string('conta_bancaria')->nullable();
            $table->string('banco')->nullable();
            $table->enum('status', ['pendente', 'conciliado', 'ignorado'])->default('pendente');
            $table->enum('tipo_conta', ['pagar', 'receber']);
            $table->unsignedBigInteger('conta_pagar_id')->nullable();
            $table->unsignedBigInteger('conta_receber_id')->nullable();
            $table->json('dados_originais')->nullable();
            $table->timestamps();
            
            $table->foreign('conta_pagar_id')->references('id')->on('contas_pagar')->onDelete('set null');
            $table->foreign('conta_receber_id')->references('id')->on('contas_receber')->onDelete('set null');
            
            $table->index(['data_transacao', 'status']);
            $table->index(['fitid', 'data_transacao']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacoes_ofx');
    }
};
