<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContaReceber extends Model
{
    protected $table = 'contas_receber';

    protected $fillable = [
        'descricao',
        'valor',
        'data_vencimento',
        'data_recebimento',
        'conta_bancaria_id',
        'cliente_id',
        'cliente_nome',
        'categoria_id',
        'tipo',
        'parcela_atual',
        'total_parcelas',
        'periodicidade',
        'data_fim_recorrencia',
        'status',
        'observacoes'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_vencimento' => 'datetime',
        'data_recebimento' => 'datetime',
        'data_fim_recorrencia' => 'datetime'
    ];

    public function contaBancaria()
    {
        return $this->belongsTo(ContaBancaria::class);
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaFinanceira::class, 'categoria_id');
    }
    
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeVencidas($query)
    {
        return $query->where('status', 'vencido')
                     ->orWhere(function($q) {
                         $q->where('status', 'pendente')
                           ->whereDate('data_vencimento', '<', now());
                     });
    }

    public function scopeRecebidas($query)
    {
        return $query->where('status', 'recebido');
    }

    public static function atualizarContasVencidas()
    {
        self::where('status', 'pendente')
            ->whereDate('data_vencimento', '<', now())
            ->update(['status' => 'vencido']);
    }

    public function estaAtrasada()
    {
        return $this->status == 'pendente' && $this->data_vencimento < now();
    }
}
