<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlanoContas;

class PlanoContasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Estrutura padrão DRE baseada no modelo brasileiro
        $estrutura = [
            // 3. RECEITAS
            [
                'codigo' => '3',
                'nome' => 'RECEITAS',
                'tipo' => 'sintetica',
                'natureza' => 'receita',
                'nivel' => 1,
                'ordem' => 100,
                'children' => [
                    // 3.1 RECEITA OPERACIONAL BRUTA
                    [
                        'codigo' => '3.1',
                        'nome' => 'RECEITA OPERACIONAL BRUTA',
                        'tipo' => 'sintetica',
                        'natureza' => 'receita',
                        'nivel' => 2,
                        'dre_tipo' => 'receita_operacional',
                        'ordem' => 110,
                        'children' => [
                            [
                                'codigo' => '3.1.1',
                                'nome' => 'Vendas de Produtos',
                                'tipo' => 'sintetica',
                                'natureza' => 'receita',
                                'nivel' => 3,
                                'ordem' => 111,
                                'children' => [
                                    ['codigo' => '3.1.1.01', 'nome' => 'Vendas à Vista', 'tipo' => 'analitica', 'natureza' => 'receita', 'nivel' => 4, 'ordem' => 1],
                                    ['codigo' => '3.1.1.02', 'nome' => 'Vendas a Prazo', 'tipo' => 'analitica', 'natureza' => 'receita', 'nivel' => 4, 'ordem' => 2],
                                    ['codigo' => '3.1.1.03', 'nome' => 'Vendas com Cartão', 'tipo' => 'analitica', 'natureza' => 'receita', 'nivel' => 4, 'ordem' => 3],
                                ]
                            ],
                            [
                                'codigo' => '3.1.2',
                                'nome' => 'Prestação de Serviços',
                                'tipo' => 'sintetica',
                                'natureza' => 'receita',
                                'nivel' => 3,
                                'ordem' => 112,
                                'children' => [
                                    ['codigo' => '3.1.2.01', 'nome' => 'Serviços de Consultoria', 'tipo' => 'analitica', 'natureza' => 'receita', 'nivel' => 4, 'ordem' => 1],
                                    ['codigo' => '3.1.2.02', 'nome' => 'Serviços de Desenvolvimento', 'tipo' => 'analitica', 'natureza' => 'receita', 'nivel' => 4, 'ordem' => 2],
                                    ['codigo' => '3.1.2.03', 'nome' => 'Serviços de Suporte', 'tipo' => 'analitica', 'natureza' => 'receita', 'nivel' => 4, 'ordem' => 3],
                                    ['codigo' => '3.1.2.04', 'nome' => 'Outros Serviços', 'tipo' => 'analitica', 'natureza' => 'receita', 'nivel' => 4, 'ordem' => 4],
                                ]
                            ],
                        ]
                    ],
                    // 3.2 DEDUÇÕES DA RECEITA
                    [
                        'codigo' => '3.2',
                        'nome' => '(-) DEDUÇÕES DA RECEITA',
                        'tipo' => 'sintetica',
                        'natureza' => 'despesa',
                        'nivel' => 2,
                        'dre_tipo' => 'deducao_receita',
                        'ordem' => 120,
                        'children' => [
                            ['codigo' => '3.2.1', 'nome' => 'Devoluções de Vendas', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 3, 'ordem' => 1],
                            ['codigo' => '3.2.2', 'nome' => 'Descontos Concedidos', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 3, 'ordem' => 2],
                            ['codigo' => '3.2.3', 'nome' => 'Impostos sobre Vendas', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 3, 'ordem' => 3],
                            ['codigo' => '3.2.4', 'nome' => 'ISS', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 3, 'ordem' => 4],
                            ['codigo' => '3.2.5', 'nome' => 'PIS', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 3, 'ordem' => 5],
                            ['codigo' => '3.2.6', 'nome' => 'COFINS', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 3, 'ordem' => 6],
                        ]
                    ],
                    // 3.3 RECEITA OPERACIONAL LÍQUIDA (calculada)
                    [
                        'codigo' => '3.3',
                        'nome' => '(=) RECEITA OPERACIONAL LÍQUIDA',
                        'tipo' => 'sintetica',
                        'natureza' => 'resultado',
                        'nivel' => 2,
                        'dre_tipo' => 'resultado',
                        'dre_formula' => '3.1 - 3.2',
                        'ordem' => 130,
                    ],
                ]
            ],
            
            // 4. CUSTOS E DESPESAS
            [
                'codigo' => '4',
                'nome' => 'CUSTOS E DESPESAS',
                'tipo' => 'sintetica',
                'natureza' => 'despesa',
                'nivel' => 1,
                'ordem' => 200,
                'children' => [
                    // 4.1 CUSTO DOS PRODUTOS/SERVIÇOS
                    [
                        'codigo' => '4.1',
                        'nome' => '(-) CUSTO DOS PRODUTOS/SERVIÇOS',
                        'tipo' => 'sintetica',
                        'natureza' => 'despesa',
                        'nivel' => 2,
                        'dre_tipo' => 'custo',
                        'ordem' => 210,
                        'children' => [
                            ['codigo' => '4.1.1', 'nome' => 'Custo de Mercadorias', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 3, 'ordem' => 1],
                            ['codigo' => '4.1.2', 'nome' => 'Custo de Matéria-Prima', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 3, 'ordem' => 2],
                            ['codigo' => '4.1.3', 'nome' => 'Mão de Obra Direta', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 3, 'ordem' => 3],
                            ['codigo' => '4.1.4', 'nome' => 'Custos Indiretos', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 3, 'ordem' => 4],
                        ]
                    ],
                    
                    // 4.2 RESULTADO BRUTO (calculado)
                    [
                        'codigo' => '4.2',
                        'nome' => '(=) RESULTADO BRUTO',
                        'tipo' => 'sintetica',
                        'natureza' => 'resultado',
                        'nivel' => 2,
                        'dre_tipo' => 'resultado',
                        'dre_formula' => '3.3 - 4.1',
                        'ordem' => 220,
                    ],
                    
                    // 4.3 DESPESAS OPERACIONAIS
                    [
                        'codigo' => '4.3',
                        'nome' => '(-) DESPESAS OPERACIONAIS',
                        'tipo' => 'sintetica',
                        'natureza' => 'despesa',
                        'nivel' => 2,
                        'dre_tipo' => 'despesa_operacional',
                        'ordem' => 230,
                        'children' => [
                            // 4.3.1 DESPESAS ADMINISTRATIVAS
                            [
                                'codigo' => '4.3.1',
                                'nome' => 'Despesas Administrativas',
                                'tipo' => 'sintetica',
                                'natureza' => 'despesa',
                                'nivel' => 3,
                                'dre_tipo' => 'despesa_administrativa',
                                'ordem' => 231,
                                'children' => [
                                    ['codigo' => '4.3.1.01', 'nome' => 'Salários e Ordenados', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 1],
                                    ['codigo' => '4.3.1.02', 'nome' => 'Encargos Sociais', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 2],
                                    ['codigo' => '4.3.1.03', 'nome' => 'Benefícios', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 3],
                                    ['codigo' => '4.3.1.04', 'nome' => 'Aluguel', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 4],
                                    ['codigo' => '4.3.1.05', 'nome' => 'Energia Elétrica', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 5],
                                    ['codigo' => '4.3.1.06', 'nome' => 'Água', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 6],
                                    ['codigo' => '4.3.1.07', 'nome' => 'Telefone e Internet', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 7],
                                    ['codigo' => '4.3.1.08', 'nome' => 'Material de Escritório', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 8],
                                    ['codigo' => '4.3.1.09', 'nome' => 'Contabilidade', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 9],
                                    ['codigo' => '4.3.1.10', 'nome' => 'Honorários Advocatícios', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 10],
                                    ['codigo' => '4.3.1.11', 'nome' => 'Seguros', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 11],
                                    ['codigo' => '4.3.1.12', 'nome' => 'Depreciação', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 12],
                                ]
                            ],
                            // 4.3.2 DESPESAS COMERCIAIS
                            [
                                'codigo' => '4.3.2',
                                'nome' => 'Despesas Comerciais',
                                'tipo' => 'sintetica',
                                'natureza' => 'despesa',
                                'nivel' => 3,
                                'dre_tipo' => 'despesa_comercial',
                                'ordem' => 232,
                                'children' => [
                                    ['codigo' => '4.3.2.01', 'nome' => 'Marketing e Publicidade', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 1],
                                    ['codigo' => '4.3.2.02', 'nome' => 'Comissões sobre Vendas', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 2],
                                    ['codigo' => '4.3.2.03', 'nome' => 'Fretes e Entregas', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 3],
                                    ['codigo' => '4.3.2.04', 'nome' => 'Viagens e Representação', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 4],
                                    ['codigo' => '4.3.2.05', 'nome' => 'Material Promocional', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 5],
                                ]
                            ],
                            // 4.3.3 DESPESAS FINANCEIRAS
                            [
                                'codigo' => '4.3.3',
                                'nome' => 'Despesas Financeiras',
                                'tipo' => 'sintetica',
                                'natureza' => 'despesa',
                                'nivel' => 3,
                                'dre_tipo' => 'despesa_financeira',
                                'ordem' => 233,
                                'children' => [
                                    ['codigo' => '4.3.3.01', 'nome' => 'Juros e Multas', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 1],
                                    ['codigo' => '4.3.3.02', 'nome' => 'Tarifas Bancárias', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 2],
                                    ['codigo' => '4.3.3.03', 'nome' => 'IOF', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 3],
                                    ['codigo' => '4.3.3.04', 'nome' => 'Taxas de Cartão', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 4],
                                    ['codigo' => '4.3.3.05', 'nome' => 'Descontos Concedidos', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 4, 'ordem' => 5],
                                ]
                            ],
                        ]
                    ],
                    
                    // 4.4 RECEITAS FINANCEIRAS
                    [
                        'codigo' => '4.4',
                        'nome' => '(+) RECEITAS FINANCEIRAS',
                        'tipo' => 'sintetica',
                        'natureza' => 'receita',
                        'nivel' => 2,
                        'dre_tipo' => 'receita_financeira',
                        'ordem' => 240,
                        'children' => [
                            ['codigo' => '4.4.1', 'nome' => 'Rendimentos de Aplicações', 'tipo' => 'analitica', 'natureza' => 'receita', 'nivel' => 3, 'ordem' => 1],
                            ['codigo' => '4.4.2', 'nome' => 'Juros Recebidos', 'tipo' => 'analitica', 'natureza' => 'receita', 'nivel' => 3, 'ordem' => 2],
                            ['codigo' => '4.4.3', 'nome' => 'Descontos Obtidos', 'tipo' => 'analitica', 'natureza' => 'receita', 'nivel' => 3, 'ordem' => 3],
                        ]
                    ],
                    
                    // 4.5 OUTRAS RECEITAS E DESPESAS
                    [
                        'codigo' => '4.5',
                        'nome' => 'OUTRAS RECEITAS E DESPESAS',
                        'tipo' => 'sintetica',
                        'natureza' => 'despesa',
                        'nivel' => 2,
                        'dre_tipo' => 'outras_despesas',
                        'ordem' => 250,
                        'children' => [
                            ['codigo' => '4.5.1', 'nome' => 'Venda de Imobilizado', 'tipo' => 'analitica', 'natureza' => 'receita', 'nivel' => 3, 'ordem' => 1],
                            ['codigo' => '4.5.2', 'nome' => 'Perda com Imobilizado', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 3, 'ordem' => 2],
                            ['codigo' => '4.5.3', 'nome' => 'Outras Receitas', 'tipo' => 'analitica', 'natureza' => 'receita', 'nivel' => 3, 'ordem' => 3],
                            ['codigo' => '4.5.4', 'nome' => 'Outras Despesas', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 3, 'ordem' => 4],
                        ]
                    ],
                    
                    // 4.6 RESULTADO ANTES DOS IMPOSTOS (calculado)
                    [
                        'codigo' => '4.6',
                        'nome' => '(=) RESULTADO ANTES DOS IMPOSTOS',
                        'tipo' => 'sintetica',
                        'natureza' => 'resultado',
                        'nivel' => 2,
                        'dre_tipo' => 'resultado',
                        'dre_formula' => '4.2 - 4.3 + 4.4 + 4.5',
                        'ordem' => 260,
                    ],
                    
                    // 4.7 IMPOSTOS SOBRE O LUCRO
                    [
                        'codigo' => '4.7',
                        'nome' => '(-) IMPOSTOS SOBRE O LUCRO',
                        'tipo' => 'sintetica',
                        'natureza' => 'despesa',
                        'nivel' => 2,
                        'dre_tipo' => 'outras_despesas',
                        'ordem' => 270,
                        'children' => [
                            ['codigo' => '4.7.1', 'nome' => 'IRPJ', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 3, 'ordem' => 1],
                            ['codigo' => '4.7.2', 'nome' => 'CSLL', 'tipo' => 'analitica', 'natureza' => 'despesa', 'nivel' => 3, 'ordem' => 2],
                        ]
                    ],
                    
                    // 4.8 RESULTADO LÍQUIDO (calculado)
                    [
                        'codigo' => '4.8',
                        'nome' => '(=) RESULTADO LÍQUIDO DO EXERCÍCIO',
                        'tipo' => 'sintetica',
                        'natureza' => 'resultado',
                        'nivel' => 2,
                        'dre_tipo' => 'resultado',
                        'dre_formula' => '4.6 - 4.7',
                        'ordem' => 280,
                    ],
                ]
            ],
        ];
        
        // Criar as contas recursivamente
        $this->criarContasRecursivamente($estrutura);
    }
    
    private function criarContasRecursivamente($contas, $parentId = null)
    {
        foreach ($contas as $conta) {
            $children = $conta['children'] ?? [];
            unset($conta['children']);
            
            $conta['parent_id'] = $parentId;
            $planoConta = PlanoContas::create($conta);
            
            if (!empty($children)) {
                $this->criarContasRecursivamente($children, $planoConta->id);
            }
        }
    }
}