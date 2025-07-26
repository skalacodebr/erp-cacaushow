<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CategoriaFinanceira;

class CategoriaFinanceiraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            // Categorias de Saída (Despesas)
            ['nome' => 'Salários e Encargos', 'tipo' => 'saida', 'tipo_custo' => 'pessoal', 'cor' => '#EF4444'],
            ['nome' => 'Benefícios', 'tipo' => 'saida', 'tipo_custo' => 'pessoal', 'cor' => '#F87171'],
            ['nome' => 'Aluguel', 'tipo' => 'saida', 'tipo_custo' => 'fixo', 'cor' => '#7C3AED'],
            ['nome' => 'Energia Elétrica', 'tipo' => 'saida', 'tipo_custo' => 'fixo', 'cor' => '#8B5CF6'],
            ['nome' => 'Água', 'tipo' => 'saida', 'tipo_custo' => 'fixo', 'cor' => '#A78BFA'],
            ['nome' => 'Internet', 'tipo' => 'saida', 'tipo_custo' => 'fixo', 'cor' => '#6366F1'],
            ['nome' => 'Telefone', 'tipo' => 'saida', 'tipo_custo' => 'fixo', 'cor' => '#818CF8'],
            ['nome' => 'Material de Escritório', 'tipo' => 'saida', 'tipo_custo' => 'variavel', 'cor' => '#F59E0B'],
            ['nome' => 'Marketing', 'tipo' => 'saida', 'tipo_custo' => 'variavel', 'cor' => '#FBBF24'],
            ['nome' => 'Impostos', 'tipo' => 'saida', 'tipo_custo' => 'fixo', 'cor' => '#DC2626'],
            ['nome' => 'Manutenção', 'tipo' => 'saida', 'tipo_custo' => 'variavel', 'cor' => '#059669'],
            ['nome' => 'Transporte', 'tipo' => 'saida', 'tipo_custo' => 'variavel', 'cor' => '#10B981'],
            ['nome' => 'Alimentação', 'tipo' => 'saida', 'tipo_custo' => 'variavel', 'cor' => '#14B8A6'],
            ['nome' => 'Software e Licenças', 'tipo' => 'saida', 'tipo_custo' => 'fixo', 'cor' => '#0891B2'],
            ['nome' => 'Equipamentos', 'tipo' => 'saida', 'tipo_custo' => 'variavel', 'cor' => '#0EA5E9'],
            ['nome' => 'Contabilidade', 'tipo' => 'saida', 'tipo_custo' => 'administrativo', 'cor' => '#6B7280'],
            ['nome' => 'Jurídico', 'tipo' => 'saida', 'tipo_custo' => 'administrativo', 'cor' => '#9CA3AF'],
            ['nome' => 'Seguros', 'tipo' => 'saida', 'tipo_custo' => 'fixo', 'cor' => '#64748B'],
            ['nome' => 'Outros Custos Fixos', 'tipo' => 'saida', 'tipo_custo' => 'fixo', 'cor' => '#475569'],
            ['nome' => 'Outros Custos Variáveis', 'tipo' => 'saida', 'tipo_custo' => 'variavel', 'cor' => '#334155'],
            
            // Categorias de Entrada (Receitas)
            ['nome' => 'Vendas de Produtos', 'tipo' => 'entrada', 'tipo_custo' => null, 'cor' => '#10B981'],
            ['nome' => 'Prestação de Serviços', 'tipo' => 'entrada', 'tipo_custo' => null, 'cor' => '#059669'],
            ['nome' => 'Consultoria', 'tipo' => 'entrada', 'tipo_custo' => null, 'cor' => '#047857'],
            ['nome' => 'Desenvolvimento de Software', 'tipo' => 'entrada', 'tipo_custo' => null, 'cor' => '#065F46'],
            ['nome' => 'Mensalidades', 'tipo' => 'entrada', 'tipo_custo' => null, 'cor' => '#22C55E'],
            ['nome' => 'Comissões', 'tipo' => 'entrada', 'tipo_custo' => null, 'cor' => '#16A34A'],
            ['nome' => 'Rendimentos Financeiros', 'tipo' => 'entrada', 'tipo_custo' => null, 'cor' => '#15803D'],
            ['nome' => 'Outras Receitas', 'tipo' => 'entrada', 'tipo_custo' => null, 'cor' => '#166534'],
        ];
        
        foreach ($categorias as $categoria) {
            CategoriaFinanceira::create([
                'nome' => $categoria['nome'],
                'tipo' => $categoria['tipo'],
                'tipo_custo' => $categoria['tipo_custo'],
                'cor' => $categoria['cor'],
                'ativo' => true
            ]);
        }
    }
}
