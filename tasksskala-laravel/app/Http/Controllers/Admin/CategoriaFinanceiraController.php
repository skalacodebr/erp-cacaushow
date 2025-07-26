<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoriaFinanceira;
use App\Models\TipoCusto;
use Illuminate\Http\Request;

class CategoriaFinanceiraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categorias = CategoriaFinanceira::with('tipoCusto')->orderBy('tipo')->orderBy('nome')->paginate(10);
        return view('admin.categorias-financeiras.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tiposCusto = TipoCusto::ativos()->ordenados()->get();
        return view('admin.categorias-financeiras.create', compact('tiposCusto'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:entrada,saida',
            'tipo_custo_id' => 'nullable|exists:tipos_custo,id',
            'cor' => 'required|string|max:7',
            'descricao' => 'nullable|string',
            'ativo' => 'boolean'
        ]);

        $validated['ativo'] = $request->has('ativo');

        CategoriaFinanceira::create($validated);

        return redirect()->route('admin.categorias-financeiras.index')
                        ->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $categoria = CategoriaFinanceira::with(['contasPagar', 'contasReceber'])->findOrFail($id);
        return view('admin.categorias-financeiras.show', compact('categoria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $categoria = CategoriaFinanceira::findOrFail($id);
        $tiposCusto = TipoCusto::ativos()->ordenados()->get();
        return view('admin.categorias-financeiras.edit', compact('categoria', 'tiposCusto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $categoria = CategoriaFinanceira::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:entrada,saida',
            'tipo_custo_id' => 'nullable|exists:tipos_custo,id',
            'cor' => 'required|string|max:7',
            'descricao' => 'nullable|string',
            'ativo' => 'boolean'
        ]);

        $validated['ativo'] = $request->has('ativo');

        $categoria->update($validated);

        return redirect()->route('admin.categorias-financeiras.index')
                        ->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $categoria = CategoriaFinanceira::findOrFail($id);
        
        if ($categoria->contasPagar()->exists() || $categoria->contasReceber()->exists()) {
            return redirect()->route('admin.categorias-financeiras.index')
                            ->with('error', 'Não é possível excluir uma categoria que possui contas vinculadas!');
        }

        $categoria->delete();

        return redirect()->route('admin.categorias-financeiras.index')
                        ->with('success', 'Categoria excluída com sucesso!');
    }
}
