<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegraImportacaoOfx;
use Illuminate\Http\Request;

class RegraImportacaoOfxController extends Controller
{
    public function index()
    {
        $regras = RegraImportacaoOfx::orderBy('prioridade', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.regras-importacao-ofx.index', compact('regras'));
    }

    public function create()
    {
        return view('admin.regras-importacao-ofx.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'tipo_conta' => 'required|in:pagar,receber',
            'campo_busca' => 'required|in:beneficiario,descricao,numero_documento,memo',
            'padrao_busca' => 'required|string',
            'prioridade' => 'integer|min:0|max:999',
        ]);

        RegraImportacaoOfx::create($request->all());

        return redirect()
            ->route('admin.regras-importacao-ofx.index')
            ->with('success', 'Regra de importação criada com sucesso!');
    }

    public function edit(RegraImportacaoOfx $regra)
    {
        return view('admin.regras-importacao-ofx.edit', compact('regra'));
    }

    public function update(Request $request, RegraImportacaoOfx $regra)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'tipo_conta' => 'required|in:pagar,receber',
            'campo_busca' => 'required|in:beneficiario,descricao,numero_documento,memo',
            'padrao_busca' => 'required|string',
            'prioridade' => 'integer|min:0|max:999',
        ]);

        $regra->update($request->all());

        return redirect()
            ->route('admin.regras-importacao-ofx.index')
            ->with('success', 'Regra de importação atualizada com sucesso!');
    }

    public function destroy(RegraImportacaoOfx $regra)
    {
        $regra->delete();

        return redirect()
            ->route('admin.regras-importacao-ofx.index')
            ->with('success', 'Regra de importação excluída com sucesso!');
    }

    public function toggle(RegraImportacaoOfx $regra)
    {
        $regra->ativo = !$regra->ativo;
        $regra->save();

        return response()->json(['ativo' => $regra->ativo]);
    }
}