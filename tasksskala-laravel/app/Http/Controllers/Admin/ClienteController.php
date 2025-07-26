<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cliente::query();

        // Busca
        if ($request->filled('busca')) {
            $query->buscar($request->busca);
        }

        // Filtro por tipo de pessoa
        if ($request->filled('tipo_pessoa')) {
            $query->where('tipo_pessoa', $request->tipo_pessoa);
        }

        // Filtro por status
        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }

        $clientes = $query->orderBy('nome')->paginate(15);

        return view('admin.clientes.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'tipo_pessoa' => 'required|in:fisica,juridica',
            'cpf_cnpj' => 'nullable|string|unique:clientes',
            'rg_ie' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'cep' => 'nullable|string|max:10',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:10',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|size:2',
            'limite_credito' => 'nullable|numeric|min:0',
            'prazo_pagamento' => 'nullable|integer|min:0',
            'ativo' => 'boolean',
            'observacoes' => 'nullable|string'
        ]);

        // Processar campo boolean
        $validated['ativo'] = $request->has('ativo');

        // Validação adicional para CPF/CNPJ
        if ($request->filled('cpf_cnpj')) {
            $cpfCnpj = preg_replace('/[^0-9]/', '', $request->cpf_cnpj);
            if ($request->tipo_pessoa == 'fisica' && strlen($cpfCnpj) != 11) {
                return back()->withErrors(['cpf_cnpj' => 'CPF deve ter 11 dígitos'])->withInput();
            }
            if ($request->tipo_pessoa == 'juridica' && strlen($cpfCnpj) != 14) {
                return back()->withErrors(['cpf_cnpj' => 'CNPJ deve ter 14 dígitos'])->withInput();
            }
        }

        Cliente::create($validated);

        return redirect()->route('admin.clientes.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cliente = Cliente::with(['contasReceber' => function($query) {
            $query->orderBy('data_vencimento', 'desc')->limit(10);
        }])->findOrFail($id);

        // Estatísticas do cliente
        $estatisticas = [
            'total_receber' => $cliente->contasReceber()->where('status', 'pendente')->sum('valor'),
            'total_recebido' => $cliente->contasReceber()->where('status', 'recebido')->sum('valor'),
            'contas_vencidas' => $cliente->contasReceber()->where('status', 'pendente')
                ->whereDate('data_vencimento', '<', now())->count(),
        ];

        return view('admin.clientes.show', compact('cliente', 'estatisticas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('admin.clientes.edit', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cliente = Cliente::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'tipo_pessoa' => 'required|in:fisica,juridica',
            'cpf_cnpj' => 'nullable|string|unique:clientes,cpf_cnpj,' . $id,
            'rg_ie' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'cep' => 'nullable|string|max:10',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:10',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|size:2',
            'limite_credito' => 'nullable|numeric|min:0',
            'prazo_pagamento' => 'nullable|integer|min:0',
            'ativo' => 'boolean',
            'observacoes' => 'nullable|string'
        ]);

        // Processar campo boolean
        $validated['ativo'] = $request->has('ativo');

        // Validação adicional para CPF/CNPJ
        if ($request->filled('cpf_cnpj')) {
            $cpfCnpj = preg_replace('/[^0-9]/', '', $request->cpf_cnpj);
            if ($request->tipo_pessoa == 'fisica' && strlen($cpfCnpj) != 11) {
                return back()->withErrors(['cpf_cnpj' => 'CPF deve ter 11 dígitos'])->withInput();
            }
            if ($request->tipo_pessoa == 'juridica' && strlen($cpfCnpj) != 14) {
                return back()->withErrors(['cpf_cnpj' => 'CNPJ deve ter 14 dígitos'])->withInput();
            }
        }

        $cliente->update($validated);

        return redirect()->route('admin.clientes.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cliente = Cliente::findOrFail($id);

        // Verificar se tem contas a receber
        if ($cliente->contasReceber()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível excluir este cliente pois existem contas a receber vinculadas.');
        }

        $cliente->delete();

        return redirect()->route('admin.clientes.index')
            ->with('success', 'Cliente excluído com sucesso!');
    }
}