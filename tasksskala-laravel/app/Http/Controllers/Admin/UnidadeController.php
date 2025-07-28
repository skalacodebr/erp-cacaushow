<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unidade;
use Illuminate\Http\Request;

class UnidadeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Unidade::query();

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('codigo', 'like', "%{$busca}%")
                  ->orWhere('cidade', 'like', "%{$busca}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'ativo') {
                $query->where('ativo', true);
            } elseif ($request->status === 'inativo') {
                $query->where('ativo', false);
            }
        }

        $unidades = $query->orderBy('nome')->paginate(10);
        
        return view('admin.unidades.index', compact('unidades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.unidades.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'codigo' => 'required|string|max:20|unique:unidades',
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|size:2',
            'cep' => 'nullable|string|max:10',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'responsavel' => 'nullable|string|max:255',
            'ativo' => 'boolean',
            'observacoes' => 'nullable|string'
        ]);

        $validated['ativo'] = $request->has('ativo');

        Unidade::create($validated);

        return redirect()->route('admin.unidades.index')
                        ->with('success', 'Unidade cadastrada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $unidade = Unidade::with(['contasPagar', 'contasReceber'])->findOrFail($id);
        
        $totalPagar = $unidade->contasPagar()->where('status', 'pendente')->sum('valor');
        $totalReceber = $unidade->contasReceber()->where('status', 'pendente')->sum('valor');
        
        return view('admin.unidades.show', compact('unidade', 'totalPagar', 'totalReceber'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $unidade = Unidade::findOrFail($id);
        return view('admin.unidades.edit', compact('unidade'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $unidade = Unidade::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'codigo' => 'required|string|max:20|unique:unidades,codigo,' . $id,
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|size:2',
            'cep' => 'nullable|string|max:10',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'responsavel' => 'nullable|string|max:255',
            'ativo' => 'boolean',
            'observacoes' => 'nullable|string'
        ]);

        $validated['ativo'] = $request->has('ativo');

        $unidade->update($validated);

        return redirect()->route('admin.unidades.index')
                        ->with('success', 'Unidade atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $unidade = Unidade::findOrFail($id);
        
        if ($unidade->contasPagar()->exists() || $unidade->contasReceber()->exists()) {
            return redirect()->route('admin.unidades.index')
                            ->with('error', 'Não é possível excluir uma unidade que possui contas vinculadas!');
        }
        
        $unidade->delete();

        return redirect()->route('admin.unidades.index')
                        ->with('success', 'Unidade excluída com sucesso!');
    }
}