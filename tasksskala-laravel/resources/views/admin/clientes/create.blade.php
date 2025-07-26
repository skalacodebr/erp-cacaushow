@extends('layouts.admin')

@section('title', 'Novo Cliente')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Novo Cliente</h1>

        <form action="{{ route('admin.clientes.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf

            <!-- Dados Básicos -->
            <h3 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Dados Básicos</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="tipo_pessoa">
                        Tipo de Pessoa *
                    </label>
                    <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('tipo_pessoa') border-red-500 @enderror" 
                            id="tipo_pessoa" 
                            name="tipo_pessoa" 
                            required
                            onchange="toggleTipoPessoa()">
                        <option value="fisica" {{ old('tipo_pessoa', 'fisica') == 'fisica' ? 'selected' : '' }}>Pessoa Física</option>
                        <option value="juridica" {{ old('tipo_pessoa') == 'juridica' ? 'selected' : '' }}>Pessoa Jurídica</option>
                    </select>
                    @error('tipo_pessoa')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nome">
                        <span id="label-nome">Nome</span> *
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

                <div id="div-nome-fantasia" style="display: none;">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nome_fantasia">
                        Nome Fantasia
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nome_fantasia') border-red-500 @enderror" 
                           id="nome_fantasia" 
                           type="text" 
                           name="nome_fantasia" 
                           value="{{ old('nome_fantasia') }}">
                    @error('nome_fantasia')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="cpf_cnpj">
                        <span id="label-documento">CPF</span>
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('cpf_cnpj') border-red-500 @enderror" 
                           id="cpf_cnpj" 
                           type="text" 
                           name="cpf_cnpj" 
                           value="{{ old('cpf_cnpj') }}"
                           placeholder="000.000.000-00">
                    @error('cpf_cnpj')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="rg_ie">
                        <span id="label-rg-ie">RG</span>
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('rg_ie') border-red-500 @enderror" 
                           id="rg_ie" 
                           type="text" 
                           name="rg_ie" 
                           value="{{ old('rg_ie') }}">
                    @error('rg_ie')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Contato -->
            <h3 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Contato</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
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

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="telefone">
                        Telefone
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('telefone') border-red-500 @enderror" 
                           id="telefone" 
                           type="text" 
                           name="telefone" 
                           value="{{ old('telefone') }}"
                           placeholder="(00) 0000-0000">
                    @error('telefone')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="celular">
                        Celular
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('celular') border-red-500 @enderror" 
                           id="celular" 
                           type="text" 
                           name="celular" 
                           value="{{ old('celular') }}"
                           placeholder="(00) 00000-0000">
                    @error('celular')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="website">
                        Website
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('website') border-red-500 @enderror" 
                           id="website" 
                           type="url" 
                           name="website" 
                           value="{{ old('website') }}"
                           placeholder="https://">
                    @error('website')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Endereço -->
            <h3 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Endereço</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="cep">
                        CEP
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('cep') border-red-500 @enderror" 
                           id="cep" 
                           type="text" 
                           name="cep" 
                           value="{{ old('cep') }}"
                           placeholder="00000-000"
                           onblur="buscarCep()">
                    @error('cep')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
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

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="numero">
                        Número
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('numero') border-red-500 @enderror" 
                           id="numero" 
                           type="text" 
                           name="numero" 
                           value="{{ old('numero') }}">
                    @error('numero')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="complemento">
                        Complemento
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('complemento') border-red-500 @enderror" 
                           id="complemento" 
                           type="text" 
                           name="complemento" 
                           value="{{ old('complemento') }}">
                    @error('complemento')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="bairro">
                        Bairro
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('bairro') border-red-500 @enderror" 
                           id="bairro" 
                           type="text" 
                           name="bairro" 
                           value="{{ old('bairro') }}">
                    @error('bairro')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

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
                    <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('estado') border-red-500 @enderror" 
                            id="estado" 
                            name="estado">
                        <option value="">Selecione...</option>
                        <option value="AC" {{ old('estado') == 'AC' ? 'selected' : '' }}>AC</option>
                        <option value="AL" {{ old('estado') == 'AL' ? 'selected' : '' }}>AL</option>
                        <option value="AP" {{ old('estado') == 'AP' ? 'selected' : '' }}>AP</option>
                        <option value="AM" {{ old('estado') == 'AM' ? 'selected' : '' }}>AM</option>
                        <option value="BA" {{ old('estado') == 'BA' ? 'selected' : '' }}>BA</option>
                        <option value="CE" {{ old('estado') == 'CE' ? 'selected' : '' }}>CE</option>
                        <option value="DF" {{ old('estado') == 'DF' ? 'selected' : '' }}>DF</option>
                        <option value="ES" {{ old('estado') == 'ES' ? 'selected' : '' }}>ES</option>
                        <option value="GO" {{ old('estado') == 'GO' ? 'selected' : '' }}>GO</option>
                        <option value="MA" {{ old('estado') == 'MA' ? 'selected' : '' }}>MA</option>
                        <option value="MT" {{ old('estado') == 'MT' ? 'selected' : '' }}>MT</option>
                        <option value="MS" {{ old('estado') == 'MS' ? 'selected' : '' }}>MS</option>
                        <option value="MG" {{ old('estado') == 'MG' ? 'selected' : '' }}>MG</option>
                        <option value="PA" {{ old('estado') == 'PA' ? 'selected' : '' }}>PA</option>
                        <option value="PB" {{ old('estado') == 'PB' ? 'selected' : '' }}>PB</option>
                        <option value="PR" {{ old('estado') == 'PR' ? 'selected' : '' }}>PR</option>
                        <option value="PE" {{ old('estado') == 'PE' ? 'selected' : '' }}>PE</option>
                        <option value="PI" {{ old('estado') == 'PI' ? 'selected' : '' }}>PI</option>
                        <option value="RJ" {{ old('estado') == 'RJ' ? 'selected' : '' }}>RJ</option>
                        <option value="RN" {{ old('estado') == 'RN' ? 'selected' : '' }}>RN</option>
                        <option value="RS" {{ old('estado') == 'RS' ? 'selected' : '' }}>RS</option>
                        <option value="RO" {{ old('estado') == 'RO' ? 'selected' : '' }}>RO</option>
                        <option value="RR" {{ old('estado') == 'RR' ? 'selected' : '' }}>RR</option>
                        <option value="SC" {{ old('estado') == 'SC' ? 'selected' : '' }}>SC</option>
                        <option value="SP" {{ old('estado') == 'SP' ? 'selected' : '' }}>SP</option>
                        <option value="SE" {{ old('estado') == 'SE' ? 'selected' : '' }}>SE</option>
                        <option value="TO" {{ old('estado') == 'TO' ? 'selected' : '' }}>TO</option>
                    </select>
                    @error('estado')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Dados Financeiros -->
            <h3 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Dados Financeiros</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="limite_credito">
                        Limite de Crédito
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('limite_credito') border-red-500 @enderror" 
                           id="limite_credito" 
                           type="number" 
                           step="0.01"
                           name="limite_credito" 
                           value="{{ old('limite_credito', 0) }}">
                    @error('limite_credito')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="prazo_pagamento">
                        Prazo de Pagamento (dias)
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('prazo_pagamento') border-red-500 @enderror" 
                           id="prazo_pagamento" 
                           type="number" 
                           name="prazo_pagamento" 
                           value="{{ old('prazo_pagamento', 30) }}">
                    @error('prazo_pagamento')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="ativo">
                        Status
                    </label>
                    <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('ativo') border-red-500 @enderror" 
                            id="ativo" 
                            name="ativo">
                        <option value="1" {{ old('ativo', 1) == 1 ? 'selected' : '' }}>Ativo</option>
                        <option value="0" {{ old('ativo') == 0 ? 'selected' : '' }}>Inativo</option>
                    </select>
                    @error('ativo')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
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

            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Cadastrar
                </button>
                <a href="{{ route('admin.clientes.index') }}" class="text-gray-600 hover:text-gray-800">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Máscara para CPF/CNPJ
document.getElementById('cpf_cnpj').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length <= 11) {
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    } else {
        value = value.substring(0, 14);
        value = value.replace(/^(\d{2})(\d)/, '$1.$2');
        value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
    }
    e.target.value = value;
});

// Máscara para telefone
document.getElementById('telefone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.substring(0, 10);
    value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
    value = value.replace(/(\d{4})(\d)/, '$1-$2');
    e.target.value = value;
});

// Máscara para celular
document.getElementById('celular').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.substring(0, 11);
    value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
    value = value.replace(/(\d{5})(\d)/, '$1-$2');
    e.target.value = value;
});

// Máscara para CEP
document.getElementById('cep').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.substring(0, 8);
    value = value.replace(/(\d{5})(\d)/, '$1-$2');
    e.target.value = value;
});

// Toggle tipo pessoa
function toggleTipoPessoa() {
    const tipo = document.getElementById('tipo_pessoa').value;
    const labelNome = document.getElementById('label-nome');
    const labelDocumento = document.getElementById('label-documento');
    const labelRgIe = document.getElementById('label-rg-ie');
    const divNomeFantasia = document.getElementById('div-nome-fantasia');
    
    if (tipo === 'juridica') {
        labelNome.textContent = 'Razão Social';
        labelDocumento.textContent = 'CNPJ';
        labelRgIe.textContent = 'Inscrição Estadual';
        divNomeFantasia.style.display = 'block';
        document.getElementById('cpf_cnpj').placeholder = '00.000.000/0000-00';
    } else {
        labelNome.textContent = 'Nome';
        labelDocumento.textContent = 'CPF';
        labelRgIe.textContent = 'RG';
        divNomeFantasia.style.display = 'none';
        document.getElementById('cpf_cnpj').placeholder = '000.000.000-00';
    }
}

// Buscar CEP
function buscarCep() {
    const cep = document.getElementById('cep').value.replace(/\D/g, '');
    if (cep.length === 8) {
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('endereco').value = data.logradouro;
                    document.getElementById('bairro').value = data.bairro;
                    document.getElementById('cidade').value = data.localidade;
                    document.getElementById('estado').value = data.uf;
                }
            })
            .catch(error => console.error('Erro ao buscar CEP:', error));
    }
}

// Executar ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    toggleTipoPessoa();
});
</script>
@endsection