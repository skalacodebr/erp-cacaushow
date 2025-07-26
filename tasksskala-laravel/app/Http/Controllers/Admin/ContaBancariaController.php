<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContaBancaria;
use Illuminate\Http\Request;

class ContaBancariaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contas = ContaBancaria::orderBy('nome')->paginate(10);
        return view('admin.contas-bancarias.index', compact('contas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.contas-bancarias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'banco' => 'required|string|max:255',
            'agencia' => 'nullable|string|max:20',
            'conta' => 'required|string|max:50',
            'tipo_conta' => 'required|in:corrente,poupanca',
            'saldo_atual' => 'nullable|numeric|min:0',
            'ativo' => 'boolean',
            'observacoes' => 'nullable|string'
        ]);

        $validated['ativo'] = $request->has('ativo');
        $validated['saldo_atual'] = $validated['saldo_atual'] ?? 0;

        ContaBancaria::create($validated);

        return redirect()->route('admin.contas-bancarias.index')
                        ->with('success', 'Conta bancária cadastrada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $conta = ContaBancaria::findOrFail($id);
        return view('admin.contas-bancarias.show', compact('conta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $conta = ContaBancaria::findOrFail($id);
        return view('admin.contas-bancarias.edit', compact('conta'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $conta = ContaBancaria::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'banco' => 'required|string|max:255',
            'agencia' => 'nullable|string|max:20',
            'conta' => 'required|string|max:50',
            'tipo_conta' => 'required|in:corrente,poupanca',
            'saldo_atual' => 'nullable|numeric|min:0',
            'ativo' => 'boolean',
            'observacoes' => 'nullable|string'
        ]);

        $validated['ativo'] = $request->has('ativo');

        $conta->update($validated);

        return redirect()->route('admin.contas-bancarias.index')
                        ->with('success', 'Conta bancária atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $conta = ContaBancaria::findOrFail($id);
        
        if ($conta->contasPagar()->exists() || $conta->contasReceber()->exists()) {
            return redirect()->route('admin.contas-bancarias.index')
                            ->with('error', 'Não é possível excluir uma conta bancária que possui movimentações!');
        }

        $conta->delete();

        return redirect()->route('admin.contas-bancarias.index')
                        ->with('success', 'Conta bancária excluída com sucesso!');
    }
}
