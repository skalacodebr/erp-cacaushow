<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AtencaoController extends Controller
{
    public function index()
    {
        $hoje = Carbon::today();
        
        // Buscar contas a pagar vencidas
        $contasPagarVencidas = ContaPagar::with(['categoria', 'fornecedor', 'unidade'])
            ->where('status', 'pendente')
            ->where('data_vencimento', '<', $hoje)
            ->orderBy('data_vencimento', 'asc')
            ->get();
            
        // Buscar contas a receber vencidas
        $contasReceberVencidas = ContaReceber::with(['categoria', 'cliente', 'unidade'])
            ->where('status', 'pendente')
            ->where('data_vencimento', '<', $hoje)
            ->orderBy('data_vencimento', 'asc')
            ->get();
            
        // Calcular totais
        $totalPagarVencido = $contasPagarVencidas->sum('valor');
        $totalReceberVencido = $contasReceberVencidas->sum('valor');
        
        return view('admin.atencao.index', compact(
            'contasPagarVencidas',
            'contasReceberVencidas',
            'totalPagarVencido',
            'totalReceberVencido'
        ));
    }
}