<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TipoCusto extends Model
{
    use HasFactory;

    protected $table = 'tipos_custo';

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'ordem',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'ordem' => 'integer'
    ];

    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->nome);
            }
        });
        
        static::updating(function ($model) {
            if ($model->isDirty('nome') && !$model->isDirty('slug')) {
                $model->slug = Str::slug($model->nome);
            }
        });
    }

    public function categorias()
    {
        return $this->hasMany(CategoriaFinanceira::class, 'tipo_custo_id');
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('ordem')->orderBy('nome');
    }
}