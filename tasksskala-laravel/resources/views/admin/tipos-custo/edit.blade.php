@extends('layouts.admin')

@section('title', 'Editar Tipo de Custo')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Editar Tipo de Custo</h1>

        <form action="{{ route('admin.tipos-custo.update', $tipoCusto) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nome">
                    Nome
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nome') border-red-500 @enderror" 
                       id="nome" 
                       type="text" 
                       name="nome" 
                       value="{{ old('nome', $tipoCusto->nome) }}" 
                       required>
                @error('nome')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="descricao">
                    Descrição
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('descricao') border-red-500 @enderror" 
                          id="descricao" 
                          name="descricao" 
                          rows="3">{{ old('descricao', $tipoCusto->descricao) }}</textarea>
                @error('descricao')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="ordem">
                    Ordem de Exibição
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('ordem') border-red-500 @enderror" 
                       id="ordem" 
                       type="number" 
                       name="ordem" 
                       value="{{ old('ordem', $tipoCusto->ordem) }}" 
                       min="0">
                @error('ordem')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="ativo" 
                           value="1" 
                           {{ old('ativo', $tipoCusto->ativo) ? 'checked' : '' }}
                           class="form-checkbox h-4 w-4 text-blue-600">
                    <span class="ml-2 text-gray-700">Ativo</span>
                </label>
            </div>

            <div class="mb-6 p-4 bg-gray-100 rounded">
                <p class="text-sm text-gray-600">
                    <strong>Slug:</strong> {{ $tipoCusto->slug }}<br>
                    <strong>Criado em:</strong> {{ $tipoCusto->created_at->format('d/m/Y H:i') }}<br>
                    <strong>Atualizado em:</strong> {{ $tipoCusto->updated_at->format('d/m/Y H:i') }}
                </p>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('admin.tipos-custo.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Cancelar
                </a>
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">
                    Atualizar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection