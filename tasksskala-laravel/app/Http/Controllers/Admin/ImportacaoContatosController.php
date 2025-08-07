<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contato;
use App\Models\ListaContatos;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class ImportacaoContatosController extends Controller
{
    public function index()
    {
        return view('admin.importacao-contatos.index');
    }
    
    public function importar(Request $request)
    {
        $request->validate([
            'arquivo' => 'required|file|mimes:xlsx,xls,csv',
            'nome_lista' => 'required|string|max:255'
        ]);
        
        $nomeLista = $request->nome_lista;
        
        $lista = ListaContatos::create([
            'nome' => $nomeLista,
            'descricao' => 'Lista importada em ' . now()->format('d/m/Y H:i'),
            'status' => 'ativa'
        ]);
        
        Excel::import(new ContatosImport($lista), $request->file('arquivo'));
        
        $lista->update(['total_contatos' => $lista->contatos()->count()]);
        
        return redirect()->route('admin.listas-contatos.show', $lista)
                         ->with('success', 'Lista importada com sucesso! Total de contatos: ' . $lista->total_contatos);
    }
}

class ContatosImport implements ToCollection
{
    protected $lista;
    
    public function __construct(ListaContatos $lista)
    {
        $this->lista = $lista;
    }
    
    public function collection(Collection $rows)
    {
        $isFirstRow = true;
        
        foreach ($rows as $row) {
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }
            
            if (empty($row[0]) || empty($row[1])) {
                continue;
            }
            
            $contato = Contato::firstOrCreate(
                ['email' => trim($row[1])],
                [
                    'nome' => trim($row[0]),
                    'telefone' => isset($row[2]) ? trim($row[2]) : null,
                    'empresa' => isset($row[3]) ? trim($row[3]) : null,
                    'cargo' => isset($row[4]) ? trim($row[4]) : null,
                    'observacoes' => isset($row[5]) ? trim($row[5]) : null,
                    'status' => 'ativo'
                ]
            );
            
            if (!$this->lista->contatos()->where('contato_id', $contato->id)->exists()) {
                $this->lista->contatos()->attach($contato->id);
            }
        }
    }
}