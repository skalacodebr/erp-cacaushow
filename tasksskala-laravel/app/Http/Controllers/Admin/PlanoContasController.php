<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlanoContas;
use Illuminate\Http\Request;

class PlanoContasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Buscar apenas contas de nível 1 (principais) com suas subcontas
        $planoContas = PlanoContas::whereNull('parent_id')
            ->with('children.children.children') // Carregar até 4 níveis
            ->orderBy('codigo')
            ->get();
            
        return view('admin.plano-contas.index', compact('planoContas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contasPai = PlanoContas::where('tipo', 'sintetica')
            ->orderBy('codigo')
            ->get();
            
        return view('admin.plano-contas.create', compact('contasPai'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|unique:plano_contas',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'parent_id' => 'nullable|exists:plano_contas,id',
            'natureza' => 'required|in:receita,despesa,resultado',
            'tipo' => 'required|in:sintetica,analitica',
            'dre_tipo' => 'nullable|string',
            'dre_visivel' => 'boolean',
            'ativo' => 'boolean'
        ]);
        
        // Processar campos boolean
        $validated['dre_visivel'] = $request->has('dre_visivel');
        $validated['ativo'] = $request->has('ativo');
        
        // Calcular nível baseado no parent
        if ($validated['parent_id']) {
            $parent = PlanoContas::find($validated['parent_id']);
            $validated['nivel'] = $parent->nivel + 1;
        } else {
            $validated['nivel'] = 1;
        }
        
        PlanoContas::create($validated);
        
        return redirect()->route('admin.plano-contas.index')
            ->with('success', 'Conta criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $planoConta = PlanoContas::with(['children', 'categorias'])->findOrFail($id);
        return view('admin.plano-contas.show', compact('planoConta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $planoConta = PlanoContas::findOrFail($id);
        $contasPai = PlanoContas::where('tipo', 'sintetica')
            ->where('id', '!=', $id)
            ->orderBy('codigo')
            ->get();
            
        return view('admin.plano-contas.edit', compact('planoConta', 'contasPai'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $planoConta = PlanoContas::findOrFail($id);
        
        $validated = $request->validate([
            'codigo' => 'required|string|unique:plano_contas,codigo,' . $id,
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'parent_id' => 'nullable|exists:plano_contas,id',
            'natureza' => 'required|in:receita,despesa,resultado',
            'tipo' => 'required|in:sintetica,analitica',
            'dre_tipo' => 'nullable|string',
            'dre_visivel' => 'boolean',
            'ativo' => 'boolean'
        ]);
        
        // Processar campos boolean
        $validated['dre_visivel'] = $request->has('dre_visivel');
        $validated['ativo'] = $request->has('ativo');
        
        // Recalcular nível se parent mudou
        if ($validated['parent_id'] !== $planoConta->parent_id) {
            if ($validated['parent_id']) {
                $parent = PlanoContas::find($validated['parent_id']);
                $validated['nivel'] = $parent->nivel + 1;
            } else {
                $validated['nivel'] = 1;
            }
        }
        
        $planoConta->update($validated);
        
        return redirect()->route('admin.plano-contas.index')
            ->with('success', 'Conta atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $planoConta = PlanoContas::findOrFail($id);
        
        // Verificar se tem categorias vinculadas
        if ($planoConta->categorias()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível excluir esta conta pois existem categorias vinculadas.');
        }
        
        // Verificar se tem contas filhas
        if ($planoConta->children()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível excluir esta conta pois existem subcontas vinculadas.');
        }
        
        $planoConta->delete();
        
        return redirect()->route('admin.plano-contas.index')
            ->with('success', 'Conta excluída com sucesso!');
    }
}