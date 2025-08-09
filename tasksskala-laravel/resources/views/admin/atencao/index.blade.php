@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-red-600">
            <i class="fas fa-exclamation-triangle mr-2"></i>Atenção - Contas Vencidas
        </h1>
        <p class="text-gray-600 mt-2">Contas que precisam de atenção imediata</p>
    </div>

    <!-- Cards de Resumo -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <div class="bg-red-100 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-money-bill-wave text-red-500 text-2xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">Total a Pagar Vencido</p>
                    <p class="text-2xl font-bold text-red-900">R$ {{ number_format($totalPagarVencido, 2, ',', '.') }}</p>
                    <p class="text-sm text-red-600">{{ $contasPagarVencidas->count() }} {{ $contasPagarVencidas->count() == 1 ? 'conta' : 'contas' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-orange-100 border-l-4 border-orange-500 p-4 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-hand-holding-usd text-orange-500 text-2xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-orange-800">Total a Receber Vencido</p>
                    <p class="text-2xl font-bold text-orange-900">R$ {{ number_format($totalReceberVencido, 2, ',', '.') }}</p>
                    <p class="text-sm text-orange-600">{{ $contasReceberVencidas->count() }} {{ $contasReceberVencidas->count() == 1 ? 'conta' : 'contas' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contas a Pagar Vencidas -->
    <div class="mb-8">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="bg-red-600 px-4 py-3">
                <h2 class="text-lg font-semibold text-white">
                    <i class="fas fa-arrow-down mr-2"></i>Contas a Pagar Vencidas
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fornecedor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dias Vencido</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($contasPagarVencidas as $conta)
                            <tr class="hover:bg-red-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $conta->descricao }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($conta->fornecedor_id && $conta->fornecedor)
                                            {{ $conta->fornecedor->nome }}
                                        @elseif($conta->fornecedor)
                                            {{ $conta->fornecedor }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($conta->categoria)
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $conta->categoria->cor }}"></div>
                                            <span class="text-sm">{{ $conta->categoria->nome }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-red-600">R$ {{ number_format($conta->valor, 2, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $conta->data_vencimento->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ $conta->data_vencimento->diffInDays(now()) }} dias
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.contas-pagar.show', $conta->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Ver</a>
                                    <a href="{{ route('admin.contas-pagar.edit', $conta->id) }}" class="text-green-600 hover:text-green-900">Pagar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    Nenhuma conta a pagar vencida!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Contas a Receber Vencidas -->
    <div>
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="bg-orange-600 px-4 py-3">
                <h2 class="text-lg font-semibold text-white">
                    <i class="fas fa-arrow-up mr-2"></i>Contas a Receber Vencidas
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dias Vencido</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($contasReceberVencidas as $conta)
                            <tr class="hover:bg-orange-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $conta->descricao }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($conta->cliente)
                                            {{ $conta->cliente->nome }}
                                        @elseif($conta->cliente_nome)
                                            {{ $conta->cliente_nome }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($conta->categoria)
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $conta->categoria->cor }}"></div>
                                            <span class="text-sm">{{ $conta->categoria->nome }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-orange-600">R$ {{ number_format($conta->valor, 2, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $conta->data_vencimento->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                        {{ $conta->data_vencimento->diffInDays(now()) }} dias
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.contas-receber.show', $conta->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Ver</a>
                                    <a href="{{ route('admin.contas-receber.edit', $conta->id) }}" class="text-green-600 hover:text-green-900">Receber</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    Nenhuma conta a receber vencida!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection