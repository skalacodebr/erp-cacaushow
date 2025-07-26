<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OfxParser;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\TransacaoOfx;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportacaoOfxController extends Controller
{
    public function index()
    {
        $importacoes = TransacaoOfx::with(['contaPagar', 'contaReceber'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.importacao-ofx.index', compact('importacoes'));
    }

    public function create()
    {
        return view('admin.importacao-ofx.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'arquivo_ofx' => 'required|file|mimes:ofx,txt,xml|max:5120',
            'tipo_conta' => 'required|in:pagar,receber'
        ]);

        try {
            DB::beginTransaction();

            $arquivo = $request->file('arquivo_ofx');
            $conteudo = file_get_contents($arquivo->getRealPath());
            
            $parser = new OfxParser($conteudo);
            $dados = $parser->parse();
            
            $transacoesImportadas = 0;
            $transacoesConciliadas = 0;
            
            foreach ($dados['transactions'] as $transacao) {
                // Verifica se a transação já foi importada
                $transacaoExistente = TransacaoOfx::where('fitid', $transacao['fitid'])
                    ->where('data_transacao', $transacao['date'])
                    ->first();
                    
                if ($transacaoExistente) {
                    continue;
                }
                
                // Cria a transação OFX
                $transacaoOfx = TransacaoOfx::create([
                    'fitid' => $transacao['fitid'],
                    'tipo' => $transacao['type'],
                    'data_transacao' => $transacao['date'],
                    'valor' => abs($transacao['amount']),
                    'descricao' => $transacao['memo'] ?? $transacao['name'] ?? '',
                    'beneficiario' => $transacao['payee'] ?? $transacao['name'] ?? '',
                    'numero_documento' => $transacao['checknum'] ?? $transacao['refnum'] ?? null,
                    'conta_bancaria' => $dados['account']['accountid'] ?? null,
                    'banco' => $dados['account']['bankid'] ?? null,
                    'status' => 'pendente',
                    'tipo_conta' => $request->tipo_conta,
                    'dados_originais' => json_encode($transacao)
                ]);
                
                $transacoesImportadas++;
                
                // Tenta conciliar automaticamente
                if ($this->tentarConciliarAutomaticamente($transacaoOfx)) {
                    $transacoesConciliadas++;
                }
            }
            
            DB::commit();
            
            return redirect()
                ->route('admin.importacao-ofx.conciliar')
                ->with('success', "Importação concluída! {$transacoesImportadas} transações importadas, {$transacoesConciliadas} conciliadas automaticamente.");
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erro ao importar arquivo: ' . $e->getMessage());
        }
    }

    public function conciliar()
    {
        $transacoesPendentes = TransacaoOfx::where('status', 'pendente')
            ->orderBy('data_transacao', 'desc')
            ->paginate(20);
            
        return view('admin.importacao-ofx.conciliar', compact('transacoesPendentes'));
    }

    public function conciliarTransacao(Request $request, TransacaoOfx $transacao)
    {
        $request->validate([
            'acao' => 'required|in:vincular,criar,ignorar',
            'conta_id' => 'required_if:acao,vincular'
        ]);

        try {
            DB::beginTransaction();

            if ($request->acao === 'vincular') {
                // Vincula a uma conta existente
                if ($transacao->tipo_conta === 'pagar') {
                    $conta = ContaPagar::findOrFail($request->conta_id);
                    $transacao->conta_pagar_id = $conta->id;
                } else {
                    $conta = ContaReceber::findOrFail($request->conta_id);
                    $transacao->conta_receber_id = $conta->id;
                }
                
                $transacao->status = 'conciliado';
                $transacao->save();
                
            } elseif ($request->acao === 'criar') {
                // Cria uma nova conta
                if ($transacao->tipo_conta === 'pagar') {
                    $conta = ContaPagar::create([
                        'descricao' => $transacao->descricao,
                        'fornecedor' => $transacao->beneficiario,
                        'valor' => $transacao->valor,
                        'data_vencimento' => $transacao->data_transacao,
                        'data_pagamento' => $transacao->data_transacao,
                        'status' => 'pago',
                        'forma_pagamento' => 'transferencia',
                        'numero_documento' => $transacao->numero_documento,
                        'observacoes' => 'Importado via OFX - ' . $transacao->fitid
                    ]);
                    
                    $transacao->conta_pagar_id = $conta->id;
                } else {
                    $conta = ContaReceber::create([
                        'descricao' => $transacao->descricao,
                        'cliente_id' => null, // Será necessário associar manualmente depois
                        'valor' => $transacao->valor,
                        'data_vencimento' => $transacao->data_transacao,
                        'data_recebimento' => $transacao->data_transacao,
                        'status' => 'recebido',
                        'forma_recebimento' => 'transferencia',
                        'numero_documento' => $transacao->numero_documento,
                        'observacoes' => 'Importado via OFX - ' . $transacao->fitid
                    ]);
                    
                    $transacao->conta_receber_id = $conta->id;
                }
                
                $transacao->status = 'conciliado';
                $transacao->save();
                
            } else {
                // Ignora a transação
                $transacao->status = 'ignorado';
                $transacao->save();
            }

            DB::commit();
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function buscarContasSugeridas(TransacaoOfx $transacao)
    {
        $query = $transacao->tipo_conta === 'pagar' ? ContaPagar::query() : ContaReceber::query();
        
        // Busca por valor aproximado (+-5%)
        $valorMin = $transacao->valor * 0.95;
        $valorMax = $transacao->valor * 1.05;
        
        $contas = $query->whereBetween('valor', [$valorMin, $valorMax])
            ->whereDate('data_vencimento', '>=', Carbon::parse($transacao->data_transacao)->subDays(30))
            ->whereDate('data_vencimento', '<=', Carbon::parse($transacao->data_transacao)->addDays(30))
            ->get();
            
        return response()->json($contas);
    }

    private function tentarConciliarAutomaticamente(TransacaoOfx $transacao)
    {
        if ($transacao->tipo_conta === 'pagar') {
            // Busca conta a pagar com valores e datas próximas
            $conta = ContaPagar::where('valor', $transacao->valor)
                ->whereDate('data_vencimento', '>=', Carbon::parse($transacao->data_transacao)->subDays(5))
                ->whereDate('data_vencimento', '<=', Carbon::parse($transacao->data_transacao)->addDays(5))
                ->whereNull('data_pagamento')
                ->first();
                
            if ($conta) {
                $transacao->conta_pagar_id = $conta->id;
                $transacao->status = 'conciliado';
                $transacao->save();
                
                // Atualiza a conta como paga
                $conta->data_pagamento = $transacao->data_transacao;
                $conta->status = 'pago';
                $conta->save();
                
                return true;
            }
        } else {
            // Busca conta a receber com valores e datas próximas
            $conta = ContaReceber::where('valor', $transacao->valor)
                ->whereDate('data_vencimento', '>=', Carbon::parse($transacao->data_transacao)->subDays(5))
                ->whereDate('data_vencimento', '<=', Carbon::parse($transacao->data_transacao)->addDays(5))
                ->whereNull('data_recebimento')
                ->first();
                
            if ($conta) {
                $transacao->conta_receber_id = $conta->id;
                $transacao->status = 'conciliado';
                $transacao->save();
                
                // Atualiza a conta como recebida
                $conta->data_recebimento = $transacao->data_transacao;
                $conta->status = 'recebido';
                $conta->save();
                
                return true;
            }
        }
        
        return false;
    }
}