<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contato extends Model
{
    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'empresa',
        'cargo',
        'observacoes',
        'status'
    ];
    
    public function listas()
    {
        return $this->belongsToMany(ListaContatos::class, 'contato_lista', 'contato_id', 'lista_id')
                    ->withTimestamps();
    }
}
