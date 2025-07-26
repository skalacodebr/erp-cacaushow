<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContaBancaria extends Model
{
    protected $table = 'contas_bancarias';

    protected $fillable = [
        'nome',
        'banco',
        'agencia',
        'conta',
        'tipo_conta',
        'saldo_atual',
        'ativo',
        'observacoes'
    ];

    protected $casts = [
        'saldo_atual' => 'decimal:2',
        'ativo' => 'boolean'
    ];

    public function contasPagar()
    {
        return $this->hasMany(ContaPagar::class);
    }

    public function contasReceber()
    {
        return $this->hasMany(ContaReceber::class);
    }
}
