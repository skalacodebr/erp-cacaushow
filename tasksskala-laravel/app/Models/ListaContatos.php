<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListaContatos extends Model
{
    protected $table = 'listas_contatos';
    
    protected $fillable = [
        'nome',
        'descricao',
        'total_contatos',
        'status'
    ];
    
    public function contatos()
    {
        return $this->belongsToMany(Contato::class, 'contato_lista', 'lista_id', 'contato_id')
                    ->withTimestamps();
    }
    
    public function campanhas()
    {
        return $this->hasMany(Campanha::class, 'lista_id');
    }
}
