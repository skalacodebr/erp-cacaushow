@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Detalhes do Fornecedor</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.fornecedores.edit', $fornecedor->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Editar
                </a>
                <a href="{{ route('admin.fornecedores.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Voltar
                </a>
            </div>
        </div>

        <!-- Informações do Fornecedor -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Informações Gerais</h2>
                <span class="px-3 py-1 text-sm rounded-full {{ $fornecedor->ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $fornecedor->ativo ? 'Ativo' : 'Inativo' }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">Nome</h3>
                    <p class="text-gray-900">{{ $fornecedor->nome }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">Tipo de Pessoa</h3>
                    <p class="text-gray-900">{{ $fornecedor->tipo_pessoa == 'fisica' ? 'Pessoa Física' : 'Pessoa Jurídica' }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">{{ $fornecedor->tipo_pessoa == 'fisica' ? 'CPF' : 'CNPJ' }}</h3>
                    <p class="text-gray-900">{{ $fornecedor->documento }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">Email</h3>
                    <p class="text-gray-900">{{ $fornecedor->email ?: '-' }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">Telefone</h3>
                    <p class="text-gray-900">{{ $fornecedor->telefone ?: '-' }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">Celular</h3>
                    <p class="text-gray-900">{{ $fornecedor->celular ?: '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Endereço -->
        @if($fornecedor->endereco || $fornecedor->cidade)
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Endereço</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($fornecedor->cep)
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">CEP</h3>
                    <p class="text-gray-900">{{ $fornecedor->cep }}</p>
                </div>
                @endif

                @if($fornecedor->endereco)
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">Endereço</h3>
                    <p class="text-gray-900">
                        {{ $fornecedor->endereco }}{{ $fornecedor->numero ? ', '.$fornecedor->numero : '' }}{{ $fornecedor->complemento ? ' - '.$fornecedor->complemento : '' }}
                    </p>
                </div>
                @endif

                @if($fornecedor->bairro)
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">Bairro</h3>
                    <p class="text-gray-900">{{ $fornecedor->bairro }}</p>
                </div>
                @endif

                @if($fornecedor->cidade)
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">Cidade/Estado</h3>
                    <p class="text-gray-900">{{ $fornecedor->cidade }}{{ $fornecedor->estado ? '/'.$fornecedor->estado : '' }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Observações -->
        @if($fornecedor->observacoes)
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Observações</h2>
            <p class="text-gray-700 whitespace-pre-wrap">{{ $fornecedor->observacoes }}</p>
        </div>
        @endif

        <!-- Resumo Financeiro -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Resumo Financeiro</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">Total de Contas</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $fornecedor->contasPagar->count() }}</p>
                </div>
                
                <div class="text-center">
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">Valor Total</h3>
                    <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($fornecedor->contasPagar->sum('valor'), 2, ',', '.') }}</p>
                </div>
                
                <div class="text-center">
                    <h3 class="text-sm font-semibold text-gray-600 mb-1">Pendentes</h3>
                    <p class="text-2xl font-bold text-red-600">{{ $fornecedor->contasPagar->where('status', 'pendente')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Últimas Contas -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Últimas Contas a Pagar</h2>
                <a href="{{ route('admin.contas-pagar.index', ['fornecedor_id' => $fornecedor->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    Ver todas →
                </a>
            </div>

            @if($fornecedor->contasPagar->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($fornecedor->contasPagar->take(10) as $conta)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $conta->descricao }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        R$ {{ number_format($conta->valor, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $conta->data_vencimento->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $conta->status == 'pago' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $conta->status == 'pendente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $conta->status == 'vencido' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $conta->status == 'cancelado' ? 'bg-gray-100 text-gray-800' : '' }}">
                                            {{ ucfirst($conta->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.contas-pagar.show', $conta->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Nenhuma conta a pagar encontrada para este fornecedor.</p>
            @endif
        </div>

        <!-- Informações Adicionais -->
        <div class="mt-6 text-sm text-gray-500">
            <p>Cadastrado em: {{ $fornecedor->created_at->format('d/m/Y H:i') }}</p>
            <p>Última atualização: {{ $fornecedor->updated_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</div>
@endsection