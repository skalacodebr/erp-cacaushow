<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaFinanceira extends Model
{
    protected $table = 'categorias_financeiras';
    
    protected $fillable = [
        'nome',
        'tipo',
        'tipo_custo',
        'tipo_custo_id',
        'plano_conta_id',
        'cor',
        'descricao',
        'ativo'
    ];
    
    protected $casts = [
        'ativo' => 'boolean'
    ];
    
    public function tipoCusto()
    {
        return $this->belongsTo(TipoCusto::class, 'tipo_custo_id');
    }
    
    public function planoConta()
    {
        return $this->belongsTo(PlanoContas::class, 'plano_conta_id');
    }
    
    public function contasPagar()
    {
        return $this->hasMany(ContaPagar::class, 'categoria_id');
    }
    
    public function contasReceber()
    {
        return $this->hasMany(ContaReceber::class, 'categoria_id');
    }
    
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }
    
    public function scopeSaidas($query)
    {
        return $query->where('tipo', 'saida');
    }
    
    public function scopeEntradas($query)
    {
        return $query->where('tipo', 'entrada');
    }
}
