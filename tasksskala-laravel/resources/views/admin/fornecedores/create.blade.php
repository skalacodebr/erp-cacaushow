@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Novo Fornecedor</h1>
            <a href="{{ route('admin.fornecedores.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Voltar
            </a>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <form method="POST" action="{{ route('admin.fornecedores.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Dados Básicos -->
                    <div class="col-span-2">
                        <h3 class="text-lg font-semibold mb-4 text-gray-700">Dados Básicos</h3>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nome <span class="text-red-500">*</span></label>
                        <input type="text" name="nome" value="{{ old('nome') }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nome') border-red-500 @enderror">
                        @error('nome')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Tipo de Pessoa <span class="text-red-500">*</span></label>
                        <select name="tipo_pessoa" id="tipo_pessoa" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('tipo_pessoa') border-red-500 @enderror">
                            <option value="">Selecione</option>
                            <option value="fisica" {{ old('tipo_pessoa') == 'fisica' ? 'selected' : '' }}>Pessoa Física</option>
                            <option value="juridica" {{ old('tipo_pessoa') == 'juridica' ? 'selected' : '' }}>Pessoa Jurídica</option>
                        </select>
                        @error('tipo_pessoa')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            <span id="label_documento">CPF/CNPJ</span>
                        </label>
                        <input type="text" name="cpf_cnpj" id="cpf_cnpj" value="{{ old('cpf_cnpj') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('cpf_cnpj') border-red-500 @enderror">
                        @error('cpf_cnpj')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Telefone</label>
                        <input type="text" name="telefone" id="telefone" value="{{ old('telefone') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('telefone') border-red-500 @enderror">
                        @error('telefone')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Celular</label>
                        <input type="text" name="celular" id="celular" value="{{ old('celular') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('celular') border-red-500 @enderror">
                        @error('celular')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Endereço -->
                    <div class="col-span-2 mt-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-700">Endereço</h3>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">CEP</label>
                        <input type="text" name="cep" id="cep" value="{{ old('cep') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('cep') border-red-500 @enderror">
                        @error('cep')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Endereço</label>
                        <input type="text" name="endereco" id="endereco" value="{{ old('endereco') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('endereco') border-red-500 @enderror">
                        @error('endereco')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Número</label>
                        <input type="text" name="numero" value="{{ old('numero') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('numero') border-red-500 @enderror">
                        @error('numero')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Complemento</label>
                        <input type="text" name="complemento" value="{{ old('complemento') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('complemento') border-red-500 @enderror">
                        @error('complemento')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Bairro</label>
                        <input type="text" name="bairro" id="bairro" value="{{ old('bairro') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('bairro') border-red-500 @enderror">
                        @error('bairro')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Cidade</label>
                        <input type="text" name="cidade" id="cidade" value="{{ old('cidade') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('cidade') border-red-500 @enderror">
                        @error('cidade')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Estado</label>
                        <select name="estado" id="estado" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('estado') border-red-500 @enderror">
                            <option value="">Selecione</option>
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

                    <!-- Observações -->
                    <div class="col-span-2 mt-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-700">Informações Adicionais</h3>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Observações</label>
                        <textarea name="observacoes" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('observacoes') border-red-500 @enderror">{{ old('observacoes') }}</textarea>
                        @error('observacoes')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="ativo" value="1" {{ old('ativo', true) ? 'checked' : '' }} class="mr-2">
                            <span class="text-sm text-gray-700">Fornecedor ativo</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Salvar
                    </button>
                    <a href="{{ route('admin.fornecedores.index') }}" class="text-gray-600 hover:text-gray-800">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Máscara para telefones
function maskPhone(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length <= 10) {
        value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
    } else {
        value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    }
    input.value = value;
}

// Máscara para CEP
function maskCEP(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.replace(/(\d{5})(\d{3})/, '$1-$2');
    input.value = value;
}

// Máscara para CPF/CNPJ
function maskCPFCNPJ(input) {
    let value = input.value.replace(/\D/g, '');
    const tipo = document.getElementById('tipo_pessoa').value;
    
    if (tipo === 'fisica') {
        value = value.substring(0, 11);
        value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    } else if (tipo === 'juridica') {
        value = value.substring(0, 14);
        value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
    }
    
    input.value = value;
}

// Event listeners
document.getElementById('telefone').addEventListener('input', function() {
    maskPhone(this);
});

document.getElementById('celular').addEventListener('input', function() {
    maskPhone(this);
});

document.getElementById('cep').addEventListener('input', function() {
    maskCEP(this);
});

document.getElementById('cpf_cnpj').addEventListener('input', function() {
    maskCPFCNPJ(this);
});

document.getElementById('tipo_pessoa').addEventListener('change', function() {
    const labelDocumento = document.getElementById('label_documento');
    const inputDocumento = document.getElementById('cpf_cnpj');
    
    if (this.value === 'fisica') {
        labelDocumento.textContent = 'CPF';
        inputDocumento.placeholder = '000.000.000-00';
    } else if (this.value === 'juridica') {
        labelDocumento.textContent = 'CNPJ';
        inputDocumento.placeholder = '00.000.000/0000-00';
    } else {
        labelDocumento.textContent = 'CPF/CNPJ';
        inputDocumento.placeholder = '';
    }
    
    inputDocumento.value = '';
});

// Busca CEP
document.getElementById('cep').addEventListener('blur', function() {
    const cep = this.value.replace(/\D/g, '');
    
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
});
</script>
@endsection