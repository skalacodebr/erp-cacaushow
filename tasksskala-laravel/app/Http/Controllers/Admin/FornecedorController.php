<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fornecedor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FornecedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Fornecedor::withCount('contasPagar');
        
        // Busca por nome, email ou documento
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('cpf_cnpj', 'like', '%' . $search . '%');
            });
        }
        
        // Filtro por status
        if ($request->filled('status')) {
            if ($request->status === 'ativos') {
                $query->where('ativo', true);
            } elseif ($request->status === 'inativos') {
                $query->where('ativo', false);
            }
        }
        
        // Filtro por tipo de pessoa
        if ($request->filled('tipo_pessoa')) {
            $query->where('tipo_pessoa', $request->tipo_pessoa);
        }
        
        $fornecedores = $query->orderBy('nome')->paginate(10)->appends($request->query());
        
        return view('admin.fornecedores.index', compact('fornecedores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.fornecedores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo_pessoa' => 'required|in:fisica,juridica',
            'cpf_cnpj' => [
                'nullable',
                'string',
                'unique:fornecedores',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value) {
                        $value = preg_replace('/[^0-9]/', '', $value);
                        
                        if ($request->tipo_pessoa === 'fisica') {
                            if (strlen($value) !== 11) {
                                $fail('O CPF deve conter 11 dígitos.');
                            } elseif (!$this->validarCPF($value)) {
                                $fail('O CPF informado é inválido.');
                            }
                        } else {
                            if (strlen($value) !== 14) {
                                $fail('O CNPJ deve conter 14 dígitos.');
                            } elseif (!$this->validarCNPJ($value)) {
                                $fail('O CNPJ informado é inválido.');
                            }
                        }
                    }
                },
            ],
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'cep' => 'nullable|string|max:10',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|size:2',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
        ]);
        
        // Remove formatação do CPF/CNPJ se fornecido
        if (isset($validated['cpf_cnpj']) && $validated['cpf_cnpj']) {
            $validated['cpf_cnpj'] = preg_replace('/[^0-9]/', '', $validated['cpf_cnpj']);
        }
        
        // Processar campo boolean
        $validated['ativo'] = $request->has('ativo');

        Fornecedor::create($validated);

        return redirect()->route('admin.fornecedores.index')
            ->with('success', 'Fornecedor criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Fornecedor $fornecedor)
    {
        $fornecedor->load(['contasPagar' => function($query) {
            $query->orderBy('data_vencimento', 'desc')->limit(10);
        }]);
        
        return view('admin.fornecedores.show', compact('fornecedor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fornecedor $fornecedor)
    {
        return view('admin.fornecedores.edit', compact('fornecedor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fornecedor $fornecedor)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo_pessoa' => 'required|in:fisica,juridica',
            'cpf_cnpj' => [
                'nullable',
                'string',
                Rule::unique('fornecedores')->ignore($fornecedor->id),
                function ($attribute, $value, $fail) use ($request) {
                    if ($value) {
                        $value = preg_replace('/[^0-9]/', '', $value);
                        
                        if ($request->tipo_pessoa === 'fisica') {
                            if (strlen($value) !== 11) {
                                $fail('O CPF deve conter 11 dígitos.');
                            } elseif (!$this->validarCPF($value)) {
                                $fail('O CPF informado é inválido.');
                            }
                        } else {
                            if (strlen($value) !== 14) {
                                $fail('O CNPJ deve conter 14 dígitos.');
                            } elseif (!$this->validarCNPJ($value)) {
                                $fail('O CNPJ informado é inválido.');
                            }
                        }
                    }
                },
            ],
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'cep' => 'nullable|string|max:10',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|size:2',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
        ]);
        
        // Remove formatação do CPF/CNPJ se fornecido
        if (isset($validated['cpf_cnpj']) && $validated['cpf_cnpj']) {
            $validated['cpf_cnpj'] = preg_replace('/[^0-9]/', '', $validated['cpf_cnpj']);
        }
        
        // Processar campo boolean
        $validated['ativo'] = $request->has('ativo');

        $fornecedor->update($validated);

        return redirect()->route('admin.fornecedores.index')
            ->with('success', 'Fornecedor atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fornecedor $fornecedor)
    {
        // Verifica se existem contas a pagar vinculadas
        if ($fornecedor->contasPagar()->exists()) {
            return redirect()->route('admin.fornecedores.index')
                ->with('error', 'Não é possível excluir este fornecedor pois existem contas a pagar vinculadas.');
        }
        
        $fornecedor->delete();

        return redirect()->route('admin.fornecedores.index')
            ->with('success', 'Fornecedor excluído com sucesso!');
    }
    
    /**
     * Valida CPF
     */
    private function validarCPF($cpf)
    {
        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        
        // Calcula o primeiro dígito verificador
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += $cpf[$i] * (10 - $i);
        }
        $resto = $soma % 11;
        $digito1 = $resto < 2 ? 0 : 11 - $resto;
        
        if ($cpf[9] != $digito1) {
            return false;
        }
        
        // Calcula o segundo dígito verificador
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += $cpf[$i] * (11 - $i);
        }
        $resto = $soma % 11;
        $digito2 = $resto < 2 ? 0 : 11 - $resto;
        
        return $cpf[10] == $digito2;
    }
    
    /**
     * Valida CNPJ
     */
    private function validarCNPJ($cnpj)
    {
        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }
        
        // Calcula o primeiro dígito verificador
        $peso = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $soma = 0;
        for ($i = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $peso[$i];
        }
        $resto = $soma % 11;
        $digito1 = $resto < 2 ? 0 : 11 - $resto;
        
        if ($cnpj[12] != $digito1) {
            return false;
        }
        
        // Calcula o segundo dígito verificador
        $peso = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $soma = 0;
        for ($i = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $peso[$i];
        }
        $resto = $soma % 11;
        $digito2 = $resto < 2 ? 0 : 11 - $resto;
        
        return $cnpj[13] == $digito2;
    }
}
