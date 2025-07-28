@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Nova Unidade</h1>

        <form action="{{ route('admin.unidades.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nome">
                    Nome da Unidade *
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nome') border-red-500 @enderror" 
                       id="nome" 
                       type="text" 
                       name="nome" 
                       value="{{ old('nome') }}" 
                       required>
                @error('nome')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="codigo">
                    Código *
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('codigo') border-red-500 @enderror" 
                       id="codigo" 
                       type="text" 
                       name="codigo" 
                       value="{{ old('codigo') }}" 
                       maxlength="20"
                       placeholder="Ex: UN001"
                       required>
                @error('codigo')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="responsavel">
                    Responsável
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('responsavel') border-red-500 @enderror" 
                       id="responsavel" 
                       type="text" 
                       name="responsavel" 
                       value="{{ old('responsavel') }}">
                @error('responsavel')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    E-mail
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror" 
                       id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}">
                @error('email')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="telefone">
                    Telefone
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('telefone') border-red-500 @enderror" 
                       id="telefone" 
                       type="text" 
                       name="telefone" 
                       value="{{ old('telefone') }}"
                       placeholder="(11) 1234-5678">
                @error('telefone')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <h3 class="text-lg font-semibold mb-3">Endereço</h3>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="endereco">
                    Endereço
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('endereco') border-red-500 @enderror" 
                       id="endereco" 
                       type="text" 
                       name="endereco" 
                       value="{{ old('endereco') }}">
                @error('endereco')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="cidade">
                        Cidade
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('cidade') border-red-500 @enderror" 
                           id="cidade" 
                           type="text" 
                           name="cidade" 
                           value="{{ old('cidade') }}">
                    @error('cidade')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="estado">
                        Estado
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('estado') border-red-500 @enderror" 
                           id="estado" 
                           type="text" 
                           name="estado" 
                           value="{{ old('estado') }}"
                           maxlength="2"
                           placeholder="UF">
                    @error('estado')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="cep">
                    CEP
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('cep') border-red-500 @enderror" 
                       id="cep" 
                       type="text" 
                       name="cep" 
                       value="{{ old('cep') }}"
                       placeholder="00000-000">
                @error('cep')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="observacoes">
                    Observações
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('observacoes') border-red-500 @enderror" 
                          id="observacoes" 
                          name="observacoes" 
                          rows="3">{{ old('observacoes') }}</textarea>
                @error('observacoes')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="ativo" 
                           value="1" 
                           {{ old('ativo', true) ? 'checked' : '' }}
                           class="mr-2">
                    <span class="text-gray-700">Unidade ativa</span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Cadastrar
                </button>
                <a href="{{ route('admin.unidades.index') }}" class="text-gray-600 hover:text-gray-800">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection