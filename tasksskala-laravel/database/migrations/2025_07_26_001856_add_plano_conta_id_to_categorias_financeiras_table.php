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
        Schema::table('categorias_financeiras', function (Blueprint $table) {
            $table->foreignId('plano_conta_id')->nullable()->after('tipo_custo_id')->constrained('plano_contas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categorias_financeiras', function (Blueprint $table) {
            $table->dropForeign(['plano_conta_id']);
            $table->dropColumn('plano_conta_id');
        });
    }
};