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
        Schema::create('campanhas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->foreignId('lista_id')->constrained('listas_contatos');
            $table->string('assunto');
            $table->text('conteudo');
            $table->enum('status', ['rascunho', 'agendada', 'enviando', 'enviada', 'pausada'])->default('rascunho');
            $table->datetime('data_envio')->nullable();
            $table->integer('total_enviados')->default(0);
            $table->integer('total_abertos')->default(0);
            $table->integer('total_cliques')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campanhas');
    }
};
