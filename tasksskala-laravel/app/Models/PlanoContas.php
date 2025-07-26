<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanoContas extends Model
{
    protected $table = 'plano_contas';

    protected $fillable = [
        'codigo',
        'nome',
        'descricao',
        'parent_id',
        'nivel',
        'natureza',
        'tipo',
        'ativo',
        'ordem',
        'dre_visivel',
        'dre_formula',
        'dre_tipo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'dre_visivel' => 'boolean',
        'nivel' => 'integer',
        'ordem' => 'integer'
    ];

    /**
     * Conta pai
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(PlanoContas::class, 'parent_id');
    }

    /**
     * Contas filhas
     */
    public function children(): HasMany
    {
        return $this->hasMany(PlanoContas::class, 'parent_id')->orderBy('codigo');
    }

    /**
     * Categorias financeiras vinculadas
     */
    public function categorias(): HasMany
    {
        return $this->hasMany(CategoriaFinanceira::class, 'plano_conta_id');
    }

    /**
     * Verifica se é conta sintética (agrupadora)
     */
    public function isSintetica(): bool
    {
        return $this->tipo === 'sintetica';
    }

    /**
     * Verifica se é conta analítica (permite lançamentos)
     */
    public function isAnalitica(): bool
    {
        return $this->tipo === 'analitica';
    }

    /**
     * Retorna o caminho completo da conta (hierarquia)
     */
    public function getCaminhoCompletoAttribute(): string
    {
        $caminho = [$this->nome];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($caminho, $parent->nome);
            $parent = $parent->parent;
        }
        
        return implode(' > ', $caminho);
    }

    /**
     * Retorna todas as contas descendentes
     */
    public function getDescendants()
    {
        $descendants = collect();
        
        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getDescendants());
        }
        
        return $descendants;
    }

    /**
     * Calcula o saldo total da conta (incluindo subcontas)
     */
    public function calcularSaldo($dataInicio = null, $dataFim = null)
    {
        $saldo = 0;
        
        // Se for conta analítica, soma os valores das categorias
        if ($this->isAnalitica()) {
            $categoriaIds = $this->categorias->pluck('id');
            
            // Somar contas a pagar
            $query = ContaPagar::whereIn('categoria_id', $categoriaIds)
                ->where('status', 'pago');
                
            if ($dataInicio) {
                $query->whereDate('data_pagamento', '>=', $dataInicio);
            }
            if ($dataFim) {
                $query->whereDate('data_pagamento', '<=', $dataFim);
            }
            
            $totalPagar = $query->sum('valor');
            
            // Somar contas a receber
            $query = ContaReceber::whereIn('categoria_id', $categoriaIds)
                ->where('status', 'recebido');
                
            if ($dataInicio) {
                $query->whereDate('data_recebimento', '>=', $dataInicio);
            }
            if ($dataFim) {
                $query->whereDate('data_recebimento', '<=', $dataFim);
            }
            
            $totalReceber = $query->sum('valor');
            
            // Calcular saldo baseado na natureza
            if ($this->natureza === 'receita') {
                $saldo = $totalReceber;
            } else {
                $saldo = $totalPagar;
            }
        }
        
        // Se for sintética, soma o saldo das contas filhas
        if ($this->isSintetica()) {
            foreach ($this->children as $child) {
                $saldo += $child->calcularSaldo($dataInicio, $dataFim);
            }
        }
        
        return $saldo;
    }

    /**
     * Scope para contas ativas
     */
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para contas de receita
     */
    public function scopeReceitas($query)
    {
        return $query->where('natureza', 'receita');
    }

    /**
     * Scope para contas de despesa
     */
    public function scopeDespesas($query)
    {
        return $query->where('natureza', 'despesa');
    }

    /**
     * Scope para contas visíveis no DRE
     */
    public function scopeDreVisiveis($query)
    {
        return $query->where('dre_visivel', true);
    }

    /**
     * Gera próximo código disponível para subconta
     */
    public function gerarProximoCodigo(): string
    {
        $ultimaFilha = $this->children()
            ->orderBy('codigo', 'desc')
            ->first();
            
        if (!$ultimaFilha) {
            return $this->codigo . '.01';
        }
        
        // Pega o último número e incrementa
        $partes = explode('.', $ultimaFilha->codigo);
        $ultimoNumero = intval(end($partes));
        $novoNumero = str_pad($ultimoNumero + 1, 2, '0', STR_PAD_LEFT);
        
        array_pop($partes);
        $partes[] = $novoNumero;
        
        return implode('.', $partes);
    }
}