<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\CategoriaFinanceira;
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
        $periodo = Carbon::create($anoAtual, $mesAtual);
        
        // Atualizar status de contas vencidas apenas uma vez por sessão
        $cacheKey = 'dashboard_financeira_atualizacao_' . $mesAtual . '_' . $anoAtual;
        if (!cache()->has($cacheKey)) {
            ContaPagar::atualizarContasVencidas();
            ContaReceber::atualizarContasVencidas();
            cache()->put($cacheKey, true, now()->addMinutes(5));
        }
        
        // Receitas e Despesas do mês - usar cache
        $cacheKeyMetricas = 'dashboard_financeira_metricas_' . $mesAtual . '_' . $anoAtual;
        $metricas = cache()->remember($cacheKeyMetricas, now()->addMinutes(5), function() use ($mesAtual, $anoAtual) {
            $receitasMes = ContaReceber::whereMonth('data_vencimento', $mesAtual)
                ->whereYear('data_vencimento', $anoAtual)
                ->where('status', 'recebido')
                ->sum('valor');
                
            $despesasMes = ContaPagar::whereMonth('data_vencimento', $mesAtual)
                ->whereYear('data_vencimento', $anoAtual)
                ->where('status', 'pago')
                ->sum('valor');
                
            return [
                'receitas' => $receitasMes,
                'despesas' => $despesasMes,
                'lucro' => $receitasMes - $despesasMes
            ];
        });
        
        $receitasMes = $metricas['receitas'];
        $despesasMes = $metricas['despesas'];
        $lucroMes = $metricas['lucro'];
        
        // Análise por categoria - Despesas (otimizado com agregação no banco)
        $despesasPorCategoria = DB::table('contas_pagar')
            ->join('categorias_financeiras', 'contas_pagar.categoria_id', '=', 'categorias_financeiras.id')
            ->whereMonth('contas_pagar.data_vencimento', $mesAtual)
            ->whereYear('contas_pagar.data_vencimento', $anoAtual)
            ->where('contas_pagar.status', 'pago')
            ->groupBy('contas_pagar.categoria_id', 'categorias_financeiras.nome', 'categorias_financeiras.cor', 'categorias_financeiras.tipo_custo')
            ->select(
                'categorias_financeiras.nome as categoria',
                'categorias_financeiras.cor',
                'categorias_financeiras.tipo_custo',
                DB::raw('SUM(contas_pagar.valor) as valor'),
                DB::raw('COUNT(*) as quantidade')
            )
            ->orderByDesc('valor')
            ->get()
            ->map(function ($item) use ($despesasMes) {
                return [
                    'categoria' => $item->categoria,
                    'cor' => $item->cor,
                    'tipo_custo' => $item->tipo_custo,
                    'valor' => (float) $item->valor,
                    'quantidade' => $item->quantidade,
                    'percentual' => $despesasMes > 0 ? round(($item->valor / $despesasMes) * 100, 2) : 0
                ];
            });
        
        // Análise por tipo de custo (otimizado com agregação no banco)
        $despesasPorTipoCusto = DB::table('contas_pagar')
            ->leftJoin('categorias_financeiras', 'contas_pagar.categoria_id', '=', 'categorias_financeiras.id')
            ->whereMonth('contas_pagar.data_vencimento', $mesAtual)
            ->whereYear('contas_pagar.data_vencimento', $anoAtual)
            ->where('contas_pagar.status', 'pago')
            ->groupBy('categorias_financeiras.tipo_custo')
            ->select(
                DB::raw('COALESCE(categorias_financeiras.tipo_custo, "outros") as tipo_custo'),
                DB::raw('SUM(contas_pagar.valor) as total')
            )
            ->pluck('total', 'tipo_custo')
            ->toArray();
            
        // Garantir que todos os tipos estejam presentes
        $despesasPorTipoCusto = array_merge([
            'fixo' => 0,
            'variavel' => 0,
            'pessoal' => 0,
            'administrativo' => 0,
            'outros' => 0
        ], $despesasPorTipoCusto);
        
        // Receitas por categoria (otimizado com agregação no banco)
        $receitasPorCategoria = DB::table('contas_receber')
            ->join('categorias_financeiras', 'contas_receber.categoria_id', '=', 'categorias_financeiras.id')
            ->whereMonth('contas_receber.data_vencimento', $mesAtual)
            ->whereYear('contas_receber.data_vencimento', $anoAtual)
            ->where('contas_receber.status', 'recebido')
            ->groupBy('contas_receber.categoria_id', 'categorias_financeiras.nome', 'categorias_financeiras.cor')
            ->select(
                'categorias_financeiras.nome as categoria',
                'categorias_financeiras.cor',
                DB::raw('SUM(contas_receber.valor) as valor'),
                DB::raw('COUNT(*) as quantidade')
            )
            ->orderByDesc('valor')
            ->get()
            ->map(function ($item) use ($receitasMes) {
                return [
                    'categoria' => $item->categoria,
                    'cor' => $item->cor,
                    'valor' => (float) $item->valor,
                    'quantidade' => $item->quantidade,
                    'percentual' => $receitasMes > 0 ? round(($item->valor / $receitasMes) * 100, 2) : 0
                ];
            });
        
        // Evolução mensal (últimos 12 meses) - otimizado com cache
        $cacheKeyEvolucao = 'dashboard_financeira_evolucao_' . $anoAtual . '_' . $mesAtual;
        $evolucaoMensal = cache()->remember($cacheKeyEvolucao, now()->addHours(1), function() {
            $evolucao = [];
            
            // Buscar todos os dados de uma vez
            $inicioRange = Carbon::now()->subMonths(11)->startOfMonth();
            $fimRange = Carbon::now()->endOfMonth();
            
            $receitasPorMes = DB::table('contas_receber')
                ->whereBetween('data_vencimento', [$inicioRange, $fimRange])
                ->where('status', 'recebido')
                ->groupBy(DB::raw('YEAR(data_vencimento)'), DB::raw('MONTH(data_vencimento)'))
                ->select(
                    DB::raw('YEAR(data_vencimento) as ano'),
                    DB::raw('MONTH(data_vencimento) as mes'),
                    DB::raw('SUM(valor) as total')
                )
                ->get()
                ->keyBy(function ($item) {
                    return $item->ano . '-' . str_pad($item->mes, 2, '0', STR_PAD_LEFT);
                });
                
            $despesasPorMes = DB::table('contas_pagar')
                ->whereBetween('data_vencimento', [$inicioRange, $fimRange])
                ->where('status', 'pago')
                ->groupBy(DB::raw('YEAR(data_vencimento)'), DB::raw('MONTH(data_vencimento)'))
                ->select(
                    DB::raw('YEAR(data_vencimento) as ano'),
                    DB::raw('MONTH(data_vencimento) as mes'),
                    DB::raw('SUM(valor) as total')
                )
                ->get()
                ->keyBy(function ($item) {
                    return $item->ano . '-' . str_pad($item->mes, 2, '0', STR_PAD_LEFT);
                });
            
            for ($i = 11; $i >= 0; $i--) {
                $data = Carbon::now()->subMonths($i);
                $chave = $data->format('Y-m');
                
                $receitas = isset($receitasPorMes[$chave]) ? (float) $receitasPorMes[$chave]->total : 0;
                $despesas = isset($despesasPorMes[$chave]) ? (float) $despesasPorMes[$chave]->total : 0;
                
                $evolucao[] = [
                    'mes' => $data->locale('pt_BR')->monthName,
                    'ano' => $data->year,
                    'receitas' => $receitas,
                    'despesas' => $despesas,
                    'lucro' => $receitas - $despesas
                ];
            }
            
            return $evolucao;
        });
        
        // Indicadores
        $margemLucro = $receitasMes > 0 ? round(($lucroMes / $receitasMes) * 100, 2) : 0;
        $percentualPessoal = $receitasMes > 0 ? round(($despesasPorTipoCusto['pessoal'] / $receitasMes) * 100, 2) : 0;
        $percentualFixo = $receitasMes > 0 ? round(($despesasPorTipoCusto['fixo'] / $receitasMes) * 100, 2) : 0;
        $percentualVariavel = $receitasMes > 0 ? round(($despesasPorTipoCusto['variavel'] / $receitasMes) * 100, 2) : 0;
        
        return view('admin.dashboard-financeira.index', compact(
            'mesAtual',
            'anoAtual',
            'receitasMes',
            'despesasMes',
            'lucroMes',
            'margemLucro',
            'despesasPorCategoria',
            'despesasPorTipoCusto',
            'receitasPorCategoria',
            'evolucaoMensal',
            'percentualPessoal',
            'percentualFixo',
            'percentualVariavel'
        ));
    }
}