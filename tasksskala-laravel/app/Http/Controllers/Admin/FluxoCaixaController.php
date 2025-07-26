<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContaBancaria;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FluxoCaixaController extends Controller
{
    public function index(Request $request)
    {
        // Atualizar status de contas vencidas
        ContaPagar::atualizarContasVencidas();
        ContaReceber::atualizarContasVencidas();

        // Filtros
        $mesAtual = $request->get('mes', Carbon::now()->month);
        $anoAtual = $request->get('ano', Carbon::now()->year);
        
        // Contas bancárias
        $contasBancarias = ContaBancaria::where('ativo', true)->get();
        
        // Contas a pagar do mês
        $contasPagar = ContaPagar::with('contaBancaria')
            ->whereMonth('data_vencimento', $mesAtual)
            ->whereYear('data_vencimento', $anoAtual)
            ->orderBy('data_vencimento')
            ->get();
            
        // Contas a receber do mês
        $contasReceber = ContaReceber::with(['contaBancaria', 'cliente'])
            ->whereMonth('data_vencimento', $mesAtual)
            ->whereYear('data_vencimento', $anoAtual)
            ->orderBy('data_vencimento')
            ->get();
        
        // Contas atrasadas (vencidas)
        $contasPagarAtrasadas = ContaPagar::with('contaBancaria')
            ->where('status', 'vencido')
            ->orderBy('data_vencimento')
            ->get();
            
        $contasReceberAtrasadas = ContaReceber::with(['contaBancaria', 'cliente'])
            ->where('status', 'vencido')
            ->orderBy('data_vencimento')
            ->get();
        
        // Cálculos do fluxo
        $saldoInicial = $contasBancarias->sum('saldo_atual');
        
        // Total a pagar (pendentes + vencidas)
        $totalPagar = $contasPagar->whereIn('status', ['pendente', 'vencido'])->sum('valor');
        $totalPago = $contasPagar->where('status', 'pago')->sum('valor');
        
        // Total a receber (pendentes + vencidas)
        $totalReceber = $contasReceber->whereIn('status', ['pendente', 'vencido'])->sum('valor');
        $totalRecebido = $contasReceber->where('status', 'recebido')->sum('valor');
        
        // Saldo previsto
        $saldoPrevisto = $saldoInicial + $totalReceber - $totalPagar;
        $saldoAtual = $saldoInicial + $totalRecebido - $totalPago;
        
        // Fluxo diário
        $fluxoDiario = $this->calcularFluxoDiario($contasPagar, $contasReceber, $mesAtual, $anoAtual);
        
        return view('admin.fluxo-caixa.index', compact(
            'contasBancarias',
            'contasPagar',
            'contasReceber',
            'contasPagarAtrasadas',
            'contasReceberAtrasadas',
            'saldoInicial',
            'totalPagar',
            'totalPago',
            'totalReceber',
            'totalRecebido',
            'saldoPrevisto',
            'saldoAtual',
            'fluxoDiario',
            'mesAtual',
            'anoAtual'
        ));
    }
    
    private function calcularFluxoDiario($contasPagar, $contasReceber, $mes, $ano)
    {
        $diasNoMes = Carbon::create($ano, $mes)->daysInMonth;
        $fluxo = [];
        
        for ($dia = 1; $dia <= $diasNoMes; $dia++) {
            $data = Carbon::create($ano, $mes, $dia);
            
            $pagarDia = $contasPagar->filter(function ($conta) use ($data) {
                return $conta->data_vencimento->isSameDay($data);
            });
            
            $receberDia = $contasReceber->filter(function ($conta) use ($data) {
                return $conta->data_vencimento->isSameDay($data);
            });
            
            $fluxo[] = [
                'data' => $data,
                'pagar' => $pagarDia->sum('valor'),
                'receber' => $receberDia->sum('valor'),
                'saldo' => $receberDia->sum('valor') - $pagarDia->sum('valor'),
                'contas_pagar' => $pagarDia,
                'contas_receber' => $receberDia
            ];
        }
        
        return $fluxo;
    }
}
