<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListaContatos;
use Illuminate\Http\Request;

class ListaContatosController extends Controller
{
    public function index()
    {
        $listas = ListaContatos::withCount('contatos')->latest()->paginate(10);
        return view('admin.listas-contatos.index', compact('listas'));
    }

    public function create()
    {
        return view('admin.listas-contatos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string'
        ]);
        
        $lista = ListaContatos::create($request->all());
        
        return redirect()->route('admin.listas-contatos.index')
                        ->with('success', 'Lista criada com sucesso!');
    }

    public function show(ListaContatos $listaContato)
    {
        $contatos = $listaContato->contatos()->paginate(20);
        return view('admin.listas-contatos.show', compact('listaContato', 'contatos'));
    }

    public function edit(ListaContatos $listaContato)
    {
        return view('admin.listas-contatos.edit', compact('listaContato'));
    }

    public function update(Request $request, ListaContatos $listaContato)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'status' => 'required|in:ativa,inativa'
        ]);
        
        $listaContato->update($request->all());
        
        return redirect()->route('admin.listas-contatos.index')
                        ->with('success', 'Lista atualizada com sucesso!');
    }

    public function destroy(ListaContatos $listaContato)
    {
        $listaContato->delete();
        
        return redirect()->route('admin.listas-contatos.index')
                        ->with('success', 'Lista removida com sucesso!');
    }
}