<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoCusto;

class TipoCustoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            [
                'nome' => 'Custos Fixos',
                'slug' => 'fixo',
                'descricao' => 'Despesas regulares e previsíveis que não variam com o volume de produção ou vendas',
                'ordem' => 1,
                'ativo' => true
            ],
            [
                'nome' => 'Custos Variáveis',
                'slug' => 'variavel',
                'descricao' => 'Despesas que variam proporcionalmente com o volume de produção ou vendas',
                'ordem' => 2,
                'ativo' => true
            ],
            [
                'nome' => 'Despesas Administrativas',
                'slug' => 'administrativo',
                'descricao' => 'Gastos relacionados à administração geral da empresa',
                'ordem' => 3,
                'ativo' => true
            ],
            [
                'nome' => 'Pessoal (Salários e Encargos)',
                'slug' => 'pessoal',
                'descricao' => 'Todas as despesas com funcionários, incluindo salários, encargos sociais e benefícios',
                'ordem' => 4,
                'ativo' => true
            ],
            [
                'nome' => 'Outros',
                'slug' => 'outros',
                'descricao' => 'Demais categorias de despesas não classificadas nos grupos anteriores',
                'ordem' => 5,
                'ativo' => true
            ]
        ];

        foreach ($tipos as $tipo) {
            TipoCusto::updateOrCreate(
                ['slug' => $tipo['slug']],
                $tipo
            );
        }
    }
}