@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Detalhes da Unidade</h1>
            <div>
                <a href="{{ route('admin.unidades.edit', $unidade->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Editar
                </a>
                <a href="{{ route('admin.unidades.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Voltar
                </a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Informações Gerais</h3>
                        <div class="space-y-2">
                            <p><span class="font-medium">Nome:</span> {{ $unidade->nome }}</p>
                            <p><span class="font-medium">Código:</span> {{ $unidade->codigo }}</p>
                            <p><span class="font-medium">Status:</span> 
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $unidade->ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $unidade->ativo ? 'Ativa' : 'Inativa' }}
                                </span>
                            </p>
                            <p><span class="font-medium">Responsável:</span> {{ $unidade->responsavel ?? '-' }}</p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-2">Contato</h3>
                        <div class="space-y-2">
                            <p><span class="font-medium">E-mail:</span> {{ $unidade->email ?? '-' }}</p>
                            <p><span class="font-medium">Telefone:</span> {{ $unidade->telefone_formatted ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Endereço</h3>
                    <div class="space-y-2">
                        <p><span class="font-medium">Endereço:</span> {{ $unidade->endereco ?? '-' }}</p>
                        <p><span class="font-medium">Cidade:</span> {{ $unidade->cidade ?? '-' }}</p>
                        <p><span class="font-medium">Estado:</span> {{ $unidade->estado ?? '-' }}</p>
                        <p><span class="font-medium">CEP:</span> {{ $unidade->cep_formatted ?? '-' }}</p>
                    </div>
                </div>

                @if($unidade->observacoes)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Observações</h3>
                    <p class="text-gray-700">{{ $unidade->observacoes }}</p>
                </div>
                @endif

                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4">Resumo Financeiro</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-red-50 p-4 rounded-lg">
                            <h4 class="font-medium text-red-800 mb-2">Total a Pagar</h4>
                            <p class="text-2xl font-bold text-red-600">R$ {{ number_format($totalPagar, 2, ',', '.') }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $unidade->contasPagar()->where('status', 'pendente')->count() }} contas pendentes</p>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg">
                            <h4 class="font-medium text-green-800 mb-2">Total a Receber</h4>
                            <p class="text-2xl font-bold text-green-600">R$ {{ number_format($totalReceber, 2, ',', '.') }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $unidade->contasReceber()->where('status', 'pendente')->count() }} contas pendentes</p>
                        </div>
                    </div>

                    <div class="mt-4 flex gap-4">
                        <a href="{{ route('admin.contas-pagar.index', ['unidade_id' => $unidade->id]) }}" 
                           class="text-blue-600 hover:text-blue-900">
                            Ver contas a pagar desta unidade →
                        </a>
                        <a href="{{ route('admin.contas-receber.index', ['unidade_id' => $unidade->id]) }}" 
                           class="text-blue-600 hover:text-blue-900">
                            Ver contas a receber desta unidade →
                        </a>
                    </div>
                </div>

                <div class="mt-6 text-sm text-gray-500">
                    <p>Criado em: {{ $unidade->created_at->format('d/m/Y H:i') }}</p>
                    <p>Atualizado em: {{ $unidade->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection