<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContaPagar;
use App\Models\ContaBancaria;
use App\Models\CategoriaFinanceira;
use App\Models\Fornecedor;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ContaPagarController extends Controller
{
    public function pagar(Request $request, string $id)
    {
        $conta = ContaPagar::findOrFail($id);
        
        $validated = $request->validate([
            'data_pagamento' => 'required|date',
            'conta_bancaria_id' => 'required|exists:contas_bancarias,id'
        ]);
        
        // Buscar a conta bancária
        $contaBancaria = ContaBancaria::findOrFail($validated['conta_bancaria_id']);
        
        // Verificar se há saldo suficiente
        if ($contaBancaria->saldo < $conta->valor) {
            return redirect()->back()->with('error', 'Saldo insuficiente na conta bancária selecionada.');
        }
        
        // Atualizar o status da conta para pago
        $conta->update([
            'status' => 'pago',
            'data_pagamento' => $validated['data_pagamento'],
            'conta_bancaria_id' => $validated['conta_bancaria_id']
        ]);
        
        // Diminuir o saldo da conta bancária
        $contaBancaria->decrement('saldo', $conta->valor);
        
        return redirect()->back()->with('success', 'Conta paga com sucesso! Saldo da conta bancária atualizado.');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ContaPagar::with(['contaBancaria', 'categoria', 'fornecedor', 'unidade']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('mes')) {
            $query->whereMonth('data_vencimento', $request->mes);
        }

        if ($request->filled('ano')) {
            $query->whereYear('data_vencimento', $request->ano);
        }
        
        if ($request->filled('unidade_id')) {
            $query->where('unidade_id', $request->unidade_id);
        }

        $contas = $query->orderBy('data_vencimento')->paginate(10);
        
        $unidades = Unidade::ativas()->orderBy('nome')->get();
        
        return view('admin.contas-pagar.index', compact('contas', 'unidades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contasBancarias = ContaBancaria::where('ativo', true)->orderBy('nome')->get();
        $categorias = CategoriaFinanceira::with('tipoCusto')
            ->where('tipo', 'saida')
            ->where('ativo', true)
            ->get();
        $fornecedores = Fornecedor::where('ativo', true)->orderBy('nome')->get();
        $unidades = Unidade::ativas()->orderBy('nome')->get();
        return view('admin.contas-pagar.create', compact('contasBancarias', 'categorias', 'fornecedores', 'unidades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'conta_bancaria_id' => 'nullable|exists:contas_bancarias,id',
            'categoria_id' => 'required|exists:categorias_financeiras,id',
            'tipo' => 'required|in:fixa,parcelada,recorrente',
            'total_parcelas' => 'required_if:tipo,parcelada|nullable|integer|min:2',
            'periodicidade' => 'required_if:tipo,recorrente|nullable|in:semanal,mensal,bimestral,trimestral,semestral,anual',
            'data_fim_recorrencia' => 'required_if:tipo,recorrente|nullable|date|after:data_vencimento',
            'fornecedor_id' => 'nullable|exists:fornecedores,id',
            'unidade_id' => 'nullable|exists:unidades,id',
            'observacoes' => 'nullable|string'
        ]);

        if ($validated['tipo'] === 'parcelada') {
            for ($i = 1; $i <= $validated['total_parcelas']; $i++) {
                $contaPagar = $validated;
                $contaPagar['parcela_atual'] = $i;
                $contaPagar['data_vencimento'] = Carbon::parse($validated['data_vencimento'])->addMonths($i - 1);
                $contaPagar['descricao'] = $validated['descricao'] . " - Parcela {$i}/{$validated['total_parcelas']}";
                ContaPagar::create($contaPagar);
            }
        } elseif ($validated['tipo'] === 'recorrente') {
            $dataAtual = Carbon::parse($validated['data_vencimento']);
            $dataFim = Carbon::parse($validated['data_fim_recorrencia']);
            
            while ($dataAtual <= $dataFim) {
                $contaPagar = $validated;
                $contaPagar['data_vencimento'] = $dataAtual->format('Y-m-d');
                ContaPagar::create($contaPagar);
                
                switch ($validated['periodicidade']) {
                    case 'semanal':
                        $dataAtual->addWeek();
                        break;
                    case 'mensal':
                        $dataAtual->addMonth();
                        break;
                    case 'bimestral':
                        $dataAtual->addMonths(2);
                        break;
                    case 'trimestral':
                        $dataAtual->addMonths(3);
                        break;
                    case 'semestral':
                        $dataAtual->addMonths(6);
                        break;
                    case 'anual':
                        $dataAtual->addYear();
                        break;
                }
            }
        } else {
            ContaPagar::create($validated);
        }

        return redirect()->route('admin.contas-pagar.index')
                        ->with('success', 'Conta a pagar cadastrada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $conta = ContaPagar::with(['contaBancaria', 'categoria'])->findOrFail($id);
        return view('admin.contas-pagar.show', compact('conta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $conta = ContaPagar::findOrFail($id);
        $contasBancarias = ContaBancaria::where('ativo', true)->orderBy('nome')->get();
        $categorias = CategoriaFinanceira::with('tipoCusto')
            ->where('tipo', 'saida')
            ->where('ativo', true)
            ->get();
        $fornecedores = Fornecedor::where('ativo', true)->orderBy('nome')->get();
        $unidades = Unidade::ativas()->orderBy('nome')->get();
        return view('admin.contas-pagar.edit', compact('conta', 'contasBancarias', 'categorias', 'fornecedores', 'unidades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $conta = ContaPagar::findOrFail($id);
        $statusAnterior = $conta->status;

        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'data_pagamento' => 'nullable|date',
            'conta_bancaria_id' => 'nullable|exists:contas_bancarias,id',
            'categoria_id' => 'required|exists:categorias_financeiras,id',
            'status' => 'required|in:pendente,pago,vencido,cancelado',
            'fornecedor_id' => 'nullable|exists:fornecedores,id',
            'unidade_id' => 'nullable|exists:unidades,id',
            'observacoes' => 'nullable|string'
        ]);

        // Verificar se o status mudou para "pago" e se há conta bancária vinculada
        if ($statusAnterior !== 'pago' && $validated['status'] === 'pago' && $validated['conta_bancaria_id']) {
            $contaBancaria = ContaBancaria::findOrFail($validated['conta_bancaria_id']);
            
            // Verificar se há saldo suficiente
            if ($contaBancaria->saldo < $validated['valor']) {
                return redirect()->back()->with('error', 'Saldo insuficiente na conta bancária selecionada.');
            }
            
            // Diminuir o saldo da conta bancária
            $contaBancaria->decrement('saldo', $validated['valor']);
        }
        
        // Verificar se o status mudou de "pago" para outro status (estorno)
        if ($statusAnterior === 'pago' && $validated['status'] !== 'pago' && $conta->conta_bancaria_id) {
            $contaBancaria = ContaBancaria::findOrFail($conta->conta_bancaria_id);
            
            // Devolver o valor para a conta bancária
            $contaBancaria->increment('saldo', $conta->valor);
        }

        $conta->update($validated);

        $mensagem = 'Conta a pagar atualizada com sucesso!';
        if ($statusAnterior !== 'pago' && $validated['status'] === 'pago') {
            $mensagem .= ' Saldo da conta bancária foi atualizado.';
        } elseif ($statusAnterior === 'pago' && $validated['status'] !== 'pago') {
            $mensagem .= ' Valor foi devolvido para a conta bancária.';
        }

        return redirect()->route('admin.contas-pagar.index')
                        ->with('success', $mensagem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $conta = ContaPagar::findOrFail($id);
        $conta->delete();

        return redirect()->route('admin.contas-pagar.index')
                        ->with('success', 'Conta a pagar excluída com sucesso!');
    }
}
