<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unidade extends Model
{
    protected $fillable = [
        'nome',
        'codigo',
        'endereco',
        'cidade',
        'estado',
        'cep',
        'telefone',
        'email',
        'responsavel',
        'ativo',
        'observacoes'
    ];

    protected $casts = [
        'ativo' => 'boolean'
    ];

    /**
     * Contas a pagar da unidade
     */
    public function contasPagar(): HasMany
    {
        return $this->hasMany(ContaPagar::class);
    }

    /**
     * Contas a receber da unidade
     */
    public function contasReceber(): HasMany
    {
        return $this->hasMany(ContaReceber::class);
    }

    /**
     * Scope para unidades ativas
     */
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
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
}