<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $fillable = [
        'nome',
        'nome_fantasia',
        'tipo_pessoa',
        'cpf_cnpj',
        'rg_ie',
        'email',
        'telefone',
        'celular',
        'website',
        'cep',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'limite_credito',
        'prazo_pagamento',
        'data_cadastro',
        'data_ultima_compra',
        'ativo',
        'observacoes'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'limite_credito' => 'decimal:2',
        'prazo_pagamento' => 'integer',
        'data_cadastro' => 'date',
        'data_ultima_compra' => 'date'
    ];

    /**
     * Contas a receber do cliente
     */
    public function contasReceber(): HasMany
    {
        return $this->hasMany(ContaReceber::class);
    }

    /**
     * Formata CPF/CNPJ para exibição
     */
    public function getCpfCnpjFormattedAttribute(): ?string
    {
        if (!$this->cpf_cnpj) return null;
        
        $doc = preg_replace('/[^0-9]/', '', $this->cpf_cnpj);
        
        if (strlen($doc) == 11) {
            // CPF: 000.000.000-00
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
        } elseif (strlen($doc) == 14) {
            // CNPJ: 00.000.000/0000-00
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $doc);
        }
        
        return $this->cpf_cnpj;
    }

    /**
     * Formata telefone para exibição
     */
    public function getTelefoneFormattedAttribute(): ?string
    {
        if (!$this->telefone) return null;
        
        $phone = preg_replace('/[^0-9]/', '', $this->telefone);
        
        if (strlen($phone) == 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $phone);
        } elseif (strlen($phone) == 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
        }
        
        return $this->telefone;
    }

    /**
     * Formata CEP para exibição
     */
    public function getCepFormattedAttribute(): ?string
    {
        if (!$this->cep) return null;
        
        $cep = preg_replace('/[^0-9]/', '', $this->cep);
        
        if (strlen($cep) == 8) {
            return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $cep);
        }
        
        return $this->cep;
    }

    /**
     * Retorna o endereço completo
     */
    public function getEnderecoCompletoAttribute(): string
    {
        $parts = [];
        
        if ($this->endereco) $parts[] = $this->endereco;
        if ($this->numero) $parts[] = $this->numero;
        if ($this->complemento) $parts[] = $this->complemento;
        if ($this->bairro) $parts[] = $this->bairro;
        if ($this->cidade) $parts[] = $this->cidade;
        if ($this->estado) $parts[] = $this->estado;
        if ($this->cep) $parts[] = 'CEP: ' . $this->cep_formatted;
        
        return implode(', ', $parts);
    }

    /**
     * Calcula o total de débitos do cliente
     */
    public function getTotalDebitosAttribute(): float
    {
        return $this->contasReceber()
            ->where('status', 'pendente')
            ->sum('valor');
    }

    /**
     * Verifica se o cliente tem crédito disponível
     */
    public function temCreditoDisponivel(float $valor = 0): bool
    {
        return ($this->limite_credito - $this->total_debitos) >= $valor;
    }

    /**
     * Scope para clientes ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para pessoa física
     */
    public function scopePessoaFisica($query)
    {
        return $query->where('tipo_pessoa', 'fisica');
    }

    /**
     * Scope para pessoa jurídica
     */
    public function scopePessoaJuridica($query)
    {
        return $query->where('tipo_pessoa', 'juridica');
    }

    /**
     * Scope para buscar por nome ou documento
     */
    public function scopeBuscar($query, $termo)
    {
        return $query->where(function($q) use ($termo) {
            $q->where('nome', 'like', "%{$termo}%")
              ->orWhere('nome_fantasia', 'like', "%{$termo}%")
              ->orWhere('cpf_cnpj', 'like', "%{$termo}%")
              ->orWhere('email', 'like', "%{$termo}%");
        });
    }

    /**
     * Mutator para limpar CPF/CNPJ antes de salvar
     */
    public function setCpfCnpjAttribute($value)
    {
        $this->attributes['cpf_cnpj'] = preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Mutator para limpar CEP antes de salvar
     */
    public function setCepAttribute($value)
    {
        $this->attributes['cep'] = preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Mutator para limpar telefone antes de salvar
     */
    public function setTelefoneAttribute($value)
    {
        $this->attributes['telefone'] = preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Mutator para limpar celular antes de salvar
     */
    public function setCelularAttribute($value)
    {
        $this->attributes['celular'] = preg_replace('/[^0-9]/', '', $value);
    }
}