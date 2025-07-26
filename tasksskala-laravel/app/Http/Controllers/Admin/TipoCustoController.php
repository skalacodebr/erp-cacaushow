<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoCusto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TipoCustoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tiposCusto = TipoCusto::ordenados()->paginate(10);
        return view('admin.tipos-custo.index', compact('tiposCusto'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tipos-custo.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'ordem' => 'nullable|integer|min:0',
            'ativo' => 'boolean'
        ]);

        $validated['ativo'] = $request->has('ativo');
        
        TipoCusto::create($validated);

        return redirect()->route('admin.tipos-custo.index')
            ->with('success', 'Tipo de custo criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoCusto $tipoCusto)
    {
        $tipoCusto->load('categorias');
        return view('admin.tipos-custo.show', compact('tipoCusto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoCusto $tipoCusto)
    {
        return view('admin.tipos-custo.edit', compact('tipoCusto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoCusto $tipoCusto)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'ordem' => 'nullable|integer|min:0',
            'ativo' => 'boolean'
        ]);

        $validated['ativo'] = $request->has('ativo');
        
        $tipoCusto->update($validated);

        return redirect()->route('admin.tipos-custo.index')
            ->with('success', 'Tipo de custo atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoCusto $tipoCusto)
    {
        if ($tipoCusto->categorias()->count() > 0) {
            return redirect()->route('admin.tipos-custo.index')
                ->with('error', 'Não é possível excluir este tipo de custo pois existem categorias vinculadas.');
        }

        $tipoCusto->delete();

        return redirect()->route('admin.tipos-custo.index')
            ->with('success', 'Tipo de custo excluído com sucesso!');
    }
}