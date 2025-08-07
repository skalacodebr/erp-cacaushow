<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campanha;
use App\Models\ListaContatos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CampanhaController extends Controller
{
    public function index()
    {
        $campanhas = Campanha::with('lista')->latest()->paginate(10);
        return view('admin.campanhas.index', compact('campanhas'));
    }

    public function create()
    {
        $listas = ListaContatos::where('status', 'ativa')
                               ->where('total_contatos', '>', 0)
                               ->get();
        return view('admin.campanhas.create', compact('listas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'lista_id' => 'required|exists:listas_contatos,id',
            'assunto' => 'required|string|max:255',
            'conteudo' => 'required|string',
            'data_envio' => 'nullable|date|after:now'
        ]);
        
        $dados = $request->all();
        if ($request->data_envio) {
            $dados['status'] = 'agendada';
        }
        
        $campanha = Campanha::create($dados);
        
        return redirect()->route('admin.campanhas.show', $campanha)
                        ->with('success', 'Campanha criada com sucesso!');
    }

    public function show(Campanha $campanha)
    {
        $campanha->load('lista');
        return view('admin.campanhas.show', compact('campanha'));
    }

    public function edit(Campanha $campanha)
    {
        $listas = ListaContatos::where('status', 'ativa')->get();
        return view('admin.campanhas.edit', compact('campanha', 'listas'));
    }

    public function update(Request $request, Campanha $campanha)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'lista_id' => 'required|exists:listas_contatos,id',
            'assunto' => 'required|string|max:255',
            'conteudo' => 'required|string',
            'data_envio' => 'nullable|date|after:now'
        ]);
        
        $campanha->update($request->all());
        
        return redirect()->route('admin.campanhas.show', $campanha)
                        ->with('success', 'Campanha atualizada com sucesso!');
    }

    public function destroy(Campanha $campanha)
    {
        if ($campanha->status == 'enviando' || $campanha->status == 'enviada') {
            return redirect()->route('admin.campanhas.index')
                            ->with('error', 'Não é possível excluir campanhas em envio ou já enviadas.');
        }
        
        $campanha->delete();
        
        return redirect()->route('admin.campanhas.index')
                        ->with('success', 'Campanha removida com sucesso!');
    }
    
    public function enviar(Campanha $campanha)
    {
        if ($campanha->status == 'enviada') {
            return redirect()->route('admin.campanhas.show', $campanha)
                            ->with('error', 'Esta campanha já foi enviada.');
        }
        
        $campanha->update(['status' => 'enviando']);
        
        $contatos = $campanha->lista->contatos()->where('status', 'ativo')->get();
        $totalEnviados = 0;
        
        foreach ($contatos as $contato) {
            try {
                Mail::raw($campanha->conteudo, function ($message) use ($contato, $campanha) {
                    $message->to($contato->email)
                            ->subject($campanha->assunto);
                });
                $totalEnviados++;
            } catch (\Exception $e) {
                continue;
            }
        }
        
        $campanha->update([
            'status' => 'enviada',
            'total_enviados' => $totalEnviados,
            'data_envio' => now()
        ]);
        
        return redirect()->route('admin.campanhas.show', $campanha)
                        ->with('success', "Campanha enviada com sucesso! Total de emails enviados: {$totalEnviados}");
    }
}