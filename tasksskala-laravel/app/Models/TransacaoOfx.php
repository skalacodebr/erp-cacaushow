<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransacaoOfx extends Model
{
    use HasFactory;

    protected $table = 'transacoes_ofx';

    protected $fillable = [
        'fitid',
        'tipo',
        'data_transacao',
        'valor',
        'descricao',
        'beneficiario',
        'numero_documento',
        'conta_bancaria',
        'banco',
        'status',
        'tipo_conta',
        'conta_pagar_id',
        'conta_receber_id',
        'dados_originais'
    ];

    protected $casts = [
        'data_transacao' => 'datetime',
        'valor' => 'decimal:2',
        'dados_originais' => 'array'
    ];

    public function contaPagar()
    {
        return $this->belongsTo(ContaPagar::class);
    }

    public function contaReceber()
    {
        return $this->belongsTo(ContaReceber::class);
    }

    public function getStatusFormatadoAttribute()
    {
        return match($this->status) {
            'pendente' => 'Pendente',
            'conciliado' => 'Conciliado',
            'ignorado' => 'Ignorado',
            default => $this->status
        };
    }

    public function getStatusCorAttribute()
    {
        return match($this->status) {
            'pendente' => 'warning',
            'conciliado' => 'success',
            'ignorado' => 'secondary',
            default => 'primary'
        };
    }
}