@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Editar Conta a Pagar</h1>

        <form action="{{ route('admin.contas-pagar.update', $conta->id) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="descricao">
                    Descrição
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('descricao') border-red-500 @enderror" 
                       id="descricao" 
                       type="text" 
                       name="descricao" 
                       value="{{ old('descricao', $conta->descricao) }}" 
                       required>
                @error('descricao')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="fornecedor_id">
                    Fornecedor
                </label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('fornecedor_id') border-red-500 @enderror" 
                        id="fornecedor_id" 
                        name="fornecedor_id">
                    <option value="">Selecione um fornecedor...</option>
                    @foreach($fornecedores as $fornecedor)
                        <option value="{{ $fornecedor->id }}" {{ old('fornecedor_id', $conta->fornecedor_id) == $fornecedor->id ? 'selected' : '' }}>
                            {{ $fornecedor->nome }} 
                            @if($fornecedor->cpf_cnpj)
                                - {{ $fornecedor->tipo_pessoa == 'fisica' ? 'CPF' : 'CNPJ' }}: {{ \Illuminate\Support\Str::mask($fornecedor->cpf_cnpj, '*', 3, -2) }}
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('fornecedor_id')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
                <div class="mt-1">
                    <a href="{{ route('admin.fornecedores.create') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        Cadastrar novo fornecedor
                    </a>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="valor">
                    Valor
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('valor') border-red-500 @enderror" 
                       id="valor" 
                       type="number" 
                       step="0.01" 
                       name="valor" 
                       value="{{ old('valor', $conta->valor) }}" 
                       required>
                @error('valor')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="data_vencimento">
                    Data de Vencimento
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('data_vencimento') border-red-500 @enderror" 
                       id="data_vencimento" 
                       type="date" 
                       name="data_vencimento" 
                       value="{{ old('data_vencimento', $conta->data_vencimento->format('Y-m-d')) }}" 
                       required>
                @error('data_vencimento')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="data_pagamento">
                    Data de Pagamento
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('data_pagamento') border-red-500 @enderror" 
                       id="data_pagamento" 
                       type="date" 
                       name="data_pagamento" 
                       value="{{ old('data_pagamento', $conta->data_pagamento?->format('Y-m-d')) }}">
                @error('data_pagamento')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                    Status
                </label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('status') border-red-500 @enderror" 
                        id="status" 
                        name="status" 
                        required>
                    <option value="pendente" {{ old('status', $conta->status) == 'pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="pago" {{ old('status', $conta->status) == 'pago' ? 'selected' : '' }}>Pago</option>
                    <option value="vencido" {{ old('status', $conta->status) == 'vencido' ? 'selected' : '' }}>Vencido</option>
                    <option value="cancelado" {{ old('status', $conta->status) == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="categoria_id">
                    Categoria
                </label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('categoria_id') border-red-500 @enderror" 
                        id="categoria_id" 
                        name="categoria_id" 
                        required>
                    <option value="">Selecione uma categoria...</option>
                    
                    @php
                        $categoriasPorTipo = $categorias->groupBy('tipo_custo_id');
                    @endphp
                    
                    @foreach($categoriasPorTipo as $tipoCustoId => $categoriasDoTipo)
                        @if($tipoCustoId)
                            @php
                                $tipoCusto = $categoriasDoTipo->first()->tipoCusto;
                            @endphp
                            <optgroup label="{{ $tipoCusto->nome }}">
                                @foreach($categoriasDoTipo as $categoria)
                                    <option value="{{ $categoria->id }}" {{ old('categoria_id', $conta->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nome }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    @endforeach
                    
                    @if(isset($categoriasPorTipo[null]) && $categoriasPorTipo[null]->count() > 0)
                        <optgroup label="Sem Classificação">
                            @foreach($categoriasPorTipo[null] as $categoria)
                                <option value="{{ $categoria->id }}" {{ old('categoria_id', $conta->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nome }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>
                @error('categoria_id')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="conta_bancaria_id">
                    Conta Bancária
                </label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('conta_bancaria_id') border-red-500 @enderror" 
                        id="conta_bancaria_id" 
                        name="conta_bancaria_id">
                    <option value="">Selecione...</option>
                    @foreach($contasBancarias as $contaBancaria)
                        <option value="{{ $contaBancaria->id }}" {{ old('conta_bancaria_id', $conta->conta_bancaria_id) == $contaBancaria->id ? 'selected' : '' }}>
                            {{ $contaBancaria->nome }} - {{ $contaBancaria->banco }}
                        </option>
                    @endforeach
                </select>
                @error('conta_bancaria_id')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="observacoes">
                    Observações
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('observacoes') border-red-500 @enderror" 
                          id="observacoes" 
                          name="observacoes" 
                          rows="3">{{ old('observacoes', $conta->observacoes) }}</textarea>
                @error('observacoes')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <!-- Informações adicionais (somente leitura) -->
            @if($conta->tipo)
                <div class="mb-4 bg-gray-100 p-4 rounded">
                    <p class="text-sm text-gray-700">
                        <strong>Tipo:</strong> {{ ucfirst($conta->tipo) }}
                        @if($conta->parcela_atual && $conta->total_parcelas)
                            - Parcela {{ $conta->parcela_atual }}/{{ $conta->total_parcelas }}
                        @endif
                        @if($conta->periodicidade)
                            - {{ ucfirst($conta->periodicidade) }}
                        @endif
                    </p>
                </div>
            @endif

            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Atualizar
                </button>
                <a href="{{ route('admin.contas-pagar.index') }}" class="text-gray-600 hover:text-gray-800">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection