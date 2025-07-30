<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\ContaBancaria;
use App\Models\CategoriaFinanceira;
use App\Models\TipoCusto;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardFinanceiraController extends Controller
{
    public function index(Request $request)
    {
        // Filtros
        $mesAtual = $request->get('mes', Carbon::now()->month);
        $anoAtual = $request->get('ano', Carbon::now()->year);
        $unidadeId = $request->get('unidade_id');
        $comparar = $request->get('comparar', false);
        $periodo = Carbon::create($anoAtual, $mesAtual);
        
        // Buscar unidades
        $unidades = Unidade::ativas()->orderBy('nome')->get();
        
        // Se comparação está ativa, buscar dados de todas as unidades
        if ($comparar) {
            $dadosComparacao = $this->getDadosComparacao($mesAtual, $anoAtual);
            
            return view('admin.dashboard-financeira.index', compact(
                'mesAtual',
                'anoAtual',
                'unidades',
                'comparar',
                'dadosComparacao'
            ));
        }
        
        // Dados de uma unidade específica ou geral
        $dados = $this->getDadosUnidade($mesAtual, $anoAtual, $unidadeId);
        
        return view('admin.dashboard-financeira.index', array_merge(
            compact('mesAtual', 'anoAtual', 'unidades', 'unidadeId', 'comparar'),
            $dados
        ));
    }
    
    private function getDadosUnidade($mes, $ano, $unidadeId = null)
    {
        // Query base para contas a pagar
        $queryPagar = ContaPagar::whereMonth('data_vencimento', $mes)
            ->whereYear('data_vencimento', $ano);
            
        // Query base para contas a receber
        $queryReceber = ContaReceber::whereMonth('data_vencimento', $mes)
            ->whereYear('data_vencimento', $ano);
            
        // Filtrar por unidade se especificado
        if ($unidadeId) {
            $queryPagar->where('unidade_id', $unidadeId);
            $queryReceber->where('unidade_id', $unidadeId);
        }
        
        // Métricas principais
        $totalReceber = (clone $queryReceber)->sum('valor');
        $totalPagar = (clone $queryPagar)->sum('valor');
        
        $totalRecebido = (clone $queryReceber)->where('status', 'recebido')->sum('valor');
        $totalPago = (clone $queryPagar)->where('status', 'pago')->sum('valor');
        
        $aReceber = (clone $queryReceber)->where('status', 'pendente')->sum('valor');
        $aPagar = (clone $queryPagar)->where('status', 'pendente')->sum('valor');
        
        $receitasVencidas = (clone $queryReceber)
            ->where('status', 'pendente')
            ->where('data_vencimento', '<', Carbon::now())
            ->sum('valor');
            
        $despesasVencidas = (clone $queryPagar)
            ->where('status', 'pendente')
            ->where('data_vencimento', '<', Carbon::now())
            ->sum('valor');
        
        $lucroRealizado = $totalRecebido - $totalPago;
        $lucroProjetado = $totalReceber - $totalPagar;
        
        // Saldo em contas bancárias
        $saldoBancario = ContaBancaria::where('ativo', true)->sum('saldo_atual');
        
        // Fluxo de caixa diário do mês
        $diasNoMes = Carbon::create($ano, $mes)->daysInMonth;
        $fluxoDiario = [];
        
        for ($dia = 1; $dia <= $diasNoMes; $dia++) {
            $data = Carbon::create($ano, $mes, $dia);
            
            $queryReceberDia = ContaReceber::whereDate('data_vencimento', $data);
            $queryPagarDia = ContaPagar::whereDate('data_vencimento', $data);
            
            if ($unidadeId) {
                $queryReceberDia->where('unidade_id', $unidadeId);
                $queryPagarDia->where('unidade_id', $unidadeId);
            }
            
            $fluxoDiario[] = [
                'dia' => $dia,
                'receitas' => $queryReceberDia->sum('valor'),
                'despesas' => $queryPagarDia->sum('valor'),
                'saldo' => $queryReceberDia->sum('valor') - $queryPagarDia->sum('valor')
            ];
        }
        
        // Top 5 despesas por categoria
        $despesasPorCategoria = DB::table('contas_pagar')
            ->join('categorias_financeiras', 'contas_pagar.categoria_id', '=', 'categorias_financeiras.id')
            ->whereMonth('contas_pagar.data_vencimento', $mes)
            ->whereYear('contas_pagar.data_vencimento', $ano)
            ->when($unidadeId, function($query) use ($unidadeId) {
                return $query->where('contas_pagar.unidade_id', $unidadeId);
            })
            ->groupBy('categorias_financeiras.id', 'categorias_financeiras.nome', 'categorias_financeiras.cor')
            ->select(
                'categorias_financeiras.nome as categoria',
                'categorias_financeiras.cor',
                DB::raw('SUM(contas_pagar.valor) as total'),
                DB::raw('COUNT(*) as quantidade')
            )
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
            
        // Top 5 receitas por categoria
        $receitasPorCategoria = DB::table('contas_receber')
            ->join('categorias_financeiras', 'contas_receber.categoria_id', '=', 'categorias_financeiras.id')
            ->whereMonth('contas_receber.data_vencimento', $mes)
            ->whereYear('contas_receber.data_vencimento', $ano)
            ->when($unidadeId, function($query) use ($unidadeId) {
                return $query->where('contas_receber.unidade_id', $unidadeId);
            })
            ->groupBy('categorias_financeiras.id', 'categorias_financeiras.nome', 'categorias_financeiras.cor')
            ->select(
                'categorias_financeiras.nome as categoria',
                'categorias_financeiras.cor',
                DB::raw('SUM(contas_receber.valor) as total'),
                DB::raw('COUNT(*) as quantidade')
            )
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
            
        // Top 5 despesas por tipo de custo
        $despesasPorTipoCusto = DB::table('contas_pagar')
            ->join('categorias_financeiras', 'contas_pagar.categoria_id', '=', 'categorias_financeiras.id')
            ->join('tipos_custo', 'categorias_financeiras.tipo_custo_id', '=', 'tipos_custo.id')
            ->whereMonth('contas_pagar.data_vencimento', $mes)
            ->whereYear('contas_pagar.data_vencimento', $ano)
            ->when($unidadeId, function($query) use ($unidadeId) {
                return $query->where('contas_pagar.unidade_id', $unidadeId);
            })
            ->groupBy('tipos_custo.id', 'tipos_custo.nome')
            ->select(
                'tipos_custo.nome as tipo_custo',
                DB::raw('SUM(contas_pagar.valor) as total'),
                DB::raw('COUNT(DISTINCT contas_pagar.id) as quantidade')
            )
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
        
        // Evolução últimos 6 meses
        $evolucao = [];
        for ($i = 5; $i >= 0; $i--) {
            $data = Carbon::now()->subMonths($i);
            
            $queryReceberEvolucao = ContaReceber::whereMonth('data_vencimento', $data->month)
                ->whereYear('data_vencimento', $data->year);
            $queryPagarEvolucao = ContaPagar::whereMonth('data_vencimento', $data->month)
                ->whereYear('data_vencimento', $data->year);
                
            if ($unidadeId) {
                $queryReceberEvolucao->where('unidade_id', $unidadeId);
                $queryPagarEvolucao->where('unidade_id', $unidadeId);
            }
            
            $evolucao[] = [
                'mes' => $data->locale('pt_BR')->shortMonthName,
                'receitas' => $queryReceberEvolucao->sum('valor'),
                'despesas' => $queryPagarEvolucao->sum('valor')
            ];
        }
        
        // Próximas contas a vencer (7 dias)
        $queryProximasReceitas = ContaReceber::with(['cliente', 'categoria'])
            ->where('status', 'pendente')
            ->whereBetween('data_vencimento', [Carbon::now(), Carbon::now()->addDays(7)])
            ->orderBy('data_vencimento');
            
        $queryProximasDespesas = ContaPagar::with(['fornecedor', 'categoria'])
            ->where('status', 'pendente')
            ->whereBetween('data_vencimento', [Carbon::now(), Carbon::now()->addDays(7)])
            ->orderBy('data_vencimento');
            
        if ($unidadeId) {
            $queryProximasReceitas->where('unidade_id', $unidadeId);
            $queryProximasDespesas->where('unidade_id', $unidadeId);
        }
        
        $proximasReceitas = $queryProximasReceitas->limit(5)->get();
        $proximasDespesas = $queryProximasDespesas->limit(5)->get();
        
        // Indicadores
        $margemLucro = $totalReceber > 0 ? round(($lucroProjetado / $totalReceber) * 100, 2) : 0;
        $inadimplencia = $totalReceber > 0 ? round(($receitasVencidas / $totalReceber) * 100, 2) : 0;
        $liquidezCorrente = $aPagar > 0 ? round($aReceber / $aPagar, 2) : 0;
        
        return compact(
            'totalReceber',
            'totalPagar',
            'totalRecebido',
            'totalPago',
            'aReceber',
            'aPagar',
            'receitasVencidas',
            'despesasVencidas',
            'lucroRealizado',
            'lucroProjetado',
            'saldoBancario',
            'fluxoDiario',
            'despesasPorCategoria',
            'receitasPorCategoria',
            'despesasPorTipoCusto',
            'evolucao',
            'proximasReceitas',
            'proximasDespesas',
            'margemLucro',
            'inadimplencia',
            'liquidezCorrente'
        );
    }
    
    private function getDadosComparacao($mes, $ano)
    {
        $unidades = Unidade::ativas()->get();
        $comparacao = [];
        
        foreach ($unidades as $unidade) {
            $queryReceber = ContaReceber::whereMonth('data_vencimento', $mes)
                ->whereYear('data_vencimento', $ano)
                ->where('unidade_id', $unidade->id);
                
            $queryPagar = ContaPagar::whereMonth('data_vencimento', $mes)
                ->whereYear('data_vencimento', $ano)
                ->where('unidade_id', $unidade->id);
            
            $totalReceber = (clone $queryReceber)->sum('valor');
            $totalPagar = (clone $queryPagar)->sum('valor');
            $totalRecebido = (clone $queryReceber)->where('status', 'recebido')->sum('valor');
            $totalPago = (clone $queryPagar)->where('status', 'pago')->sum('valor');
            
            $comparacao[] = [
                'unidade' => $unidade,
                'receitas' => $totalReceber,
                'despesas' => $totalPagar,
                'recebido' => $totalRecebido,
                'pago' => $totalPago,
                'lucro' => $totalReceber - $totalPagar,
                'lucro_realizado' => $totalRecebido - $totalPago,
                'margem' => $totalReceber > 0 ? round((($totalReceber - $totalPagar) / $totalReceber) * 100, 2) : 0
            ];
        }
        
        // Ordenar por lucro
        usort($comparacao, function($a, $b) {
            return $b['lucro'] <=> $a['lucro'];
        });
        
        // Dados consolidados
        $totais = [
            'receitas' => array_sum(array_column($comparacao, 'receitas')),
            'despesas' => array_sum(array_column($comparacao, 'despesas')),
            'recebido' => array_sum(array_column($comparacao, 'recebido')),
            'pago' => array_sum(array_column($comparacao, 'pago')),
            'lucro' => array_sum(array_column($comparacao, 'lucro')),
            'lucro_realizado' => array_sum(array_column($comparacao, 'lucro_realizado'))
        ];
        
        $totais['margem'] = $totais['receitas'] > 0 ? 
            round(($totais['lucro'] / $totais['receitas']) * 100, 2) : 0;
        
        return [
            'comparacao' => $comparacao,
            'totais' => $totais
        ];
    }
}