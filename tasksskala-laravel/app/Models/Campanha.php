<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campanha extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'lista_id',
        'assunto',
        'conteudo',
        'status',
        'data_envio',
        'total_enviados',
        'total_abertos',
        'total_cliques'
    ];
    
    protected $casts = [
        'data_envio' => 'datetime'
    ];
    
    public function lista()
    {
        return $this->belongsTo(ListaContatos::class, 'lista_id');
    }
}
