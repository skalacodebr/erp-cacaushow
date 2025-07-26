<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{
    protected $table = 'fornecedores';
    
    protected $fillable = [
        'nome',
        'tipo_pessoa',
        'cpf_cnpj',
        'email',
        'telefone',
        'celular',
        'cep',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'observacoes',
        'ativo'
    ];
    
    protected $casts = [
        'ativo' => 'boolean'
    ];
    
    public function contasPagar()
    {
        return $this->hasMany(ContaPagar::class);
    }
    
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }
    
    public function getDocumentoAttribute()
    {
        if (!$this->cpf_cnpj) {
            return '';
        }
        
        if ($this->tipo_pessoa == 'fisica') {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->cpf_cnpj);
        } else {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $this->cpf_cnpj);
        }
    }
}
