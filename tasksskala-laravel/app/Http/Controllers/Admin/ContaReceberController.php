<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContaReceber;
use App\Models\ContaBancaria;
use App\Models\CategoriaFinanceira;
use App\Models\Cliente;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ContaReceberController extends Controller
{
    public function receber(Request $request, string $id)
    {
        $conta = ContaReceber::findOrFail($id);
        
        $validated = $request->validate([
            'data_recebimento' => 'required|date',
            'conta_bancaria_id' => 'required|exists:contas_bancarias,id'
        ]);
        
        // Buscar a conta bancária
        $contaBancaria = ContaBancaria::findOrFail($validated['conta_bancaria_id']);
        
        // Atualizar o status da conta para recebido
        $conta->update([
            'status' => 'recebido',
            'data_recebimento' => $validated['data_recebimento'],
            'conta_bancaria_id' => $validated['conta_bancaria_id']
        ]);
        
        // Aumentar o saldo da conta bancária
        $contaBancaria->increment('saldo', $conta->valor);
        
        return redirect()->back()->with('success', 'Conta recebida com sucesso! Saldo da conta bancária atualizado.');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ContaReceber::with(['contaBancaria', 'categoria', 'cliente', 'unidade']);

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
        
        return view('admin.contas-receber.index', compact('contas', 'unidades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contasBancarias = ContaBancaria::where('ativo', true)->orderBy('nome')->get();
        $categorias = CategoriaFinanceira::where('tipo', 'entrada')->orderBy('nome')->get();
        $clientes = Cliente::ativos()->orderBy('nome')->get();
        $unidades = Unidade::ativas()->orderBy('nome')->get();
        return view('admin.contas-receber.create', compact('contasBancarias', 'categorias', 'clientes', 'unidades'));
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
            'cliente_id' => 'nullable|exists:clientes,id',
            'cliente_nome' => 'nullable|string|max:255',
            'unidade_id' => 'nullable|exists:unidades,id',
            'tipo' => 'required|in:fixa,parcelada,recorrente',
            'total_parcelas' => 'required_if:tipo,parcelada|nullable|integer|min:2',
            'periodicidade' => 'required_if:tipo,recorrente|nullable|in:semanal,mensal,bimestral,trimestral,semestral,anual',
            'data_fim_recorrencia' => 'required_if:tipo,recorrente|nullable|date|after:data_vencimento',
            'categoria_id' => 'nullable|exists:categorias_financeiras,id',
            'observacoes' => 'nullable|string'
        ]);

        if ($validated['tipo'] === 'parcelada') {
            for ($i = 1; $i <= $validated['total_parcelas']; $i++) {
                $contaReceber = $validated;
                $contaReceber['parcela_atual'] = $i;
                $contaReceber['data_vencimento'] = Carbon::parse($validated['data_vencimento'])->addMonths($i - 1);
                $contaReceber['descricao'] = $validated['descricao'] . " - Parcela {$i}/{$validated['total_parcelas']}";
                ContaReceber::create($contaReceber);
            }
        } elseif ($validated['tipo'] === 'recorrente') {
            $dataAtual = Carbon::parse($validated['data_vencimento']);
            $dataFim = Carbon::parse($validated['data_fim_recorrencia']);
            
            while ($dataAtual <= $dataFim) {
                $contaReceber = $validated;
                $contaReceber['data_vencimento'] = $dataAtual->format('Y-m-d');
                ContaReceber::create($contaReceber);
                
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
            ContaReceber::create($validated);
        }

        return redirect()->route('admin.contas-receber.index')
                        ->with('success', 'Conta a receber cadastrada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $conta = ContaReceber::with(['contaBancaria', 'cliente', 'categoria'])->findOrFail($id);
        return view('admin.contas-receber.show', compact('conta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $conta = ContaReceber::findOrFail($id);
        $contasBancarias = ContaBancaria::where('ativo', true)->orderBy('nome')->get();
        $categorias = CategoriaFinanceira::where('tipo', 'entrada')->orderBy('nome')->get();
        $clientes = Cliente::ativos()->orderBy('nome')->get();
        $unidades = Unidade::ativas()->orderBy('nome')->get();
        return view('admin.contas-receber.edit', compact('conta', 'contasBancarias', 'categorias', 'clientes', 'unidades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $conta = ContaReceber::findOrFail($id);
        $statusAnterior = $conta->status;

        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'data_recebimento' => 'nullable|date',
            'conta_bancaria_id' => 'nullable|exists:contas_bancarias,id',
            'cliente_id' => 'nullable|exists:clientes,id',
            'cliente_nome' => 'nullable|string|max:255',
            'unidade_id' => 'nullable|exists:unidades,id',
            'status' => 'required|in:pendente,recebido,vencido,cancelado',
            'categoria_id' => 'nullable|exists:categorias_financeiras,id',
            'observacoes' => 'nullable|string'
        ]);

        // Verificar se o status mudou para "recebido" e se há conta bancária vinculada
        if ($statusAnterior !== 'recebido' && $validated['status'] === 'recebido' && $validated['conta_bancaria_id']) {
            $contaBancaria = ContaBancaria::findOrFail($validated['conta_bancaria_id']);
            
            // Aumentar o saldo da conta bancária
            $contaBancaria->increment('saldo', $validated['valor']);
        }
        
        // Verificar se o status mudou de "recebido" para outro status (estorno)
        if ($statusAnterior === 'recebido' && $validated['status'] !== 'recebido' && $conta->conta_bancaria_id) {
            $contaBancaria = ContaBancaria::findOrFail($conta->conta_bancaria_id);
            
            // Verificar se há saldo suficiente para o estorno
            if ($contaBancaria->saldo < $conta->valor) {
                return redirect()->back()->with('error', 'Saldo insuficiente na conta bancária para realizar o estorno.');
            }
            
            // Subtrair o valor da conta bancária
            $contaBancaria->decrement('saldo', $conta->valor);
        }

        $conta->update($validated);

        $mensagem = 'Conta a receber atualizada com sucesso!';
        if ($statusAnterior !== 'recebido' && $validated['status'] === 'recebido') {
            $mensagem .= ' Saldo da conta bancária foi atualizado.';
        } elseif ($statusAnterior === 'recebido' && $validated['status'] !== 'recebido') {
            $mensagem .= ' Valor foi estornado da conta bancária.';
        }

        return redirect()->route('admin.contas-receber.index')
                        ->with('success', $mensagem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $conta = ContaReceber::findOrFail($id);
        $conta->delete();

        return redirect()->route('admin.contas-receber.index')
                        ->with('success', 'Conta a receber excluída com sucesso!');
    }
}
