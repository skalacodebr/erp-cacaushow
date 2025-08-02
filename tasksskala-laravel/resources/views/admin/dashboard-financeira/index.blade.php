@extends('layouts.admin')

@section('title', 'Dashboard Financeira')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header com Filtros -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <h1 class="text-3xl font-bold text-gray-800 mb-4 lg:mb-0">Dashboard Financeira</h1>
            
            <form method="GET" action="{{ route('admin.dashboard-financeira.index') }}" class="flex flex-wrap gap-3">
                <select name="mes" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $mesAtual == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->locale('pt_BR')->monthName }}
                        </option>
                    @endfor
                </select>
                
                <select name="ano" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @for($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                        <option value="{{ $i }}" {{ $anoAtual == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
                
                <select name="unidade_id" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas as Unidades</option>
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}" {{ request('unidade_id') == $unidade->id ? 'selected' : '' }}>
                            {{ $unidade->nome }}
                        </option>
                    @endforeach
                </select>
                
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Filtrar
                </button>
                
                <a href="{{ route('admin.dashboard-financeira.index', array_merge(request()->all(), ['comparar' => !$comparar])) }}" 
                   class="px-6 py-2 {{ $comparar ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-600 hover:bg-gray-700' }} text-white rounded-lg transition">
                    {{ $comparar ? 'Voltar' : 'Comparar Unidades' }}
                </a>
            </form>
        </div>
    </div>

    @if($comparar)
        <!-- Modo Comparação entre Unidades -->
        <div class="grid grid-cols-1 gap-6">
            <!-- Resumo Geral -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Resumo Consolidado</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Receita Total</p>
                        <p class="text-2xl font-bold text-green-600">R$ {{ number_format($dadosComparacao['totais']['receitas'], 2, ',', '.') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Despesa Total</p>
                        <p class="text-2xl font-bold text-red-600">R$ {{ number_format($dadosComparacao['totais']['despesas'], 2, ',', '.') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Lucro Total</p>
                        <p class="text-2xl font-bold {{ $dadosComparacao['totais']['lucro'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                            R$ {{ number_format($dadosComparacao['totais']['lucro'], 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Margem Média</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $dadosComparacao['totais']['margem'] }}%</p>
                    </div>
                </div>
            </div>

            <!-- Tabela Comparativa -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-bold mb-4">Comparação entre Unidades</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidade</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Receitas</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Despesas</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Lucro</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Margem</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Realizado</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($dadosComparacao['comparacao'] as $dados)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $dados['unidade']->nome }}</div>
                                        <div class="text-sm text-gray-500">{{ $dados['unidade']->codigo }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-green-600 font-medium">
                                    R$ {{ number_format($dados['receitas'], 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-red-600 font-medium">
                                    R$ {{ number_format($dados['despesas'], 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium {{ $dados['lucro'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                    R$ {{ number_format($dados['lucro'], 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $dados['margem'] >= 20 ? 'bg-green-100 text-green-800' : ($dados['margem'] >= 10 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $dados['margem'] }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm {{ $dados['lucro_realizado'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    R$ {{ number_format($dados['lucro_realizado'], 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    <a href="{{ route('admin.dashboard-financeira.index', ['mes' => $mesAtual, 'ano' => $anoAtual, 'unidade_id' => $dados['unidade']->id]) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        Ver Detalhes
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Gráfico Comparativo -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Visualização Comparativa</h2>
                <div style="position: relative; height: 400px;">
                    <canvas id="graficoComparacao"></canvas>
                </div>
            </div>
        </div>

    @else
        <!-- Dashboard Normal -->
        <!-- Cards de Métricas Principais -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Card Receitas -->
            <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Total a Receber</p>
                        <p class="text-3xl font-bold">R$ {{ number_format($totalReceber, 2, ',', '.') }}</p>
                        <p class="text-sm mt-2">
                            <span class="text-green-200">Recebido:</span> 
                            <span class="font-semibold">R$ {{ number_format($totalRecebido, 2, ',', '.') }}</span>
                        </p>
                    </div>
                    <i class="fas fa-arrow-down text-4xl text-green-200"></i>
                </div>
            </div>

            <!-- Card Despesas -->
            <div class="bg-gradient-to-br from-red-400 to-red-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm">Total a Pagar</p>
                        <p class="text-3xl font-bold">R$ {{ number_format($totalPagar, 2, ',', '.') }}</p>
                        <p class="text-sm mt-2">
                            <span class="text-red-200">Pago:</span> 
                            <span class="font-semibold">R$ {{ number_format($totalPago, 2, ',', '.') }}</span>
                        </p>
                    </div>
                    <i class="fas fa-arrow-up text-4xl text-red-200"></i>
                </div>
            </div>

            <!-- Card Lucro -->
            <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Lucro Projetado</p>
                        <p class="text-3xl font-bold">R$ {{ number_format($lucroProjetado, 2, ',', '.') }}</p>
                        <p class="text-sm mt-2">
                            <span class="text-blue-200">Realizado:</span> 
                            <span class="font-semibold">R$ {{ number_format($lucroRealizado, 2, ',', '.') }}</span>
                        </p>
                    </div>
                    <i class="fas fa-chart-line text-4xl text-blue-200"></i>
                </div>
            </div>

            <!-- Card Saldo -->
            <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Saldo em Contas</p>
                        <p class="text-3xl font-bold">R$ {{ number_format($saldoBancario, 2, ',', '.') }}</p>
                        <p class="text-sm mt-2">
                            <span class="text-purple-200">Liquidez:</span> 
                            <span class="font-semibold">{{ $liquidezCorrente }}</span>
                        </p>
                    </div>
                    <i class="fas fa-university text-4xl text-purple-200"></i>
                </div>
            </div>
        </div>

        <!-- Indicadores -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Margem de Lucro</p>
                        <p class="text-2xl font-bold {{ $margemLucro >= 20 ? 'text-green-600' : ($margemLucro >= 10 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $margemLucro }}%
                        </p>
                    </div>
                    <div class="w-16 h-16">
                        <canvas id="gaugeMargemLucro"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Taxa de Inadimplência</p>
                        <p class="text-2xl font-bold {{ $inadimplencia <= 5 ? 'text-green-600' : ($inadimplencia <= 10 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $inadimplencia }}%
                        </p>
                    </div>
                    <div class="w-16 h-16">
                        <canvas id="gaugeInadimplencia"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Contas Vencidas</p>
                        <p class="text-2xl font-bold text-red-600">
                            R$ {{ number_format($receitasVencidas + $despesasVencidas, 2, ',', '.') }}
                        </p>
                    </div>
                    <i class="fas fa-exclamation-triangle text-3xl text-red-400"></i>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Fluxo de Caixa Diário -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Fluxo de Caixa Diário</h2>
                <div style="position: relative; height: 300px;">
                    <canvas id="graficoFluxoDiario"></canvas>
                </div>
            </div>

            <!-- Evolução Mensal -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Evolução Últimos 6 Meses</h2>
                <div style="position: relative; height: 300px;">
                    <canvas id="graficoEvolucao"></canvas>
                </div>
            </div>
        </div>

        <!-- Análise por Categoria -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Top Despesas -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Top 5 Despesas por Categoria</h2>
                @if($despesasPorCategoria->count() > 0)
                    <div class="space-y-3">
                        @foreach($despesasPorCategoria as $categoria)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ $categoria->cor ?? '#6B7280' }}"></div>
                                <span class="text-sm">{{ $categoria->categoria }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-semibold">R$ {{ number_format($categoria->total, 2, ',', '.') }}</span>
                                <span class="text-xs text-gray-500 ml-2">({{ $categoria->quantidade }})</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div style="position: relative; height: 200px;" class="mt-4">
                        <canvas id="graficoDespesasCategoria"></canvas>
                    </div>
                @else
                    <p class="text-gray-500">Nenhuma despesa registrada no período.</p>
                @endif
            </div>

            <!-- Top Receitas -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Top 10 Receitas por Categoria</h2>
                @if($receitasPorCategoria->count() > 0)
                    <div class="space-y-3">
                        @foreach($receitasPorCategoria as $categoria)
                        <div class="flex items-center justify-between mb-2 cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors"
                             onclick="abrirModalReceita({{ $categoria->categoria_id }}, '{{ $categoria->categoria }}', {{ $mesAtual }}, {{ $anoAtual }}, {{ $unidadeId ?? 'null' }})">
                            <div class="flex items-center">
                                <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ $categoria->cor ?? '#10B981' }}"></div>
                                <span class="text-sm font-medium">{{ $categoria->categoria }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-semibold">R$ {{ number_format($categoria->total, 2, ',', '.') }}</span>
                                <span class="text-xs text-gray-500 ml-2">({{ $categoria->quantidade }} {{ $categoria->quantidade == 1 ? 'lançamento' : 'lançamentos' }})</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div style="position: relative; height: 200px;" class="mt-4">
                        <canvas id="graficoReceitasCategoria"></canvas>
                    </div>
                @else
                    <p class="text-gray-500">Nenhuma receita registrada no período.</p>
                @endif
            </div>
            
            <!-- Top Despesas por Tipo de Custo -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Top 10 Despesas por Tipo de Custo</h2>
                @if($despesasPorTipoCusto->count() > 0)
                    <div class="space-y-3">
                        @foreach($despesasPorTipoCusto as $index => $tipo)
                        <div class="flex items-center justify-between mb-2 cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors"
                             onclick="abrirModalTipoCusto('{{ $tipo->tipo_custo }}', {{ $mesAtual }}, {{ $anoAtual }}, {{ $unidadeId ?? 'null' }})">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full mr-3 flex items-center justify-center text-white font-bold"
                                     style="background-color: {{ ['#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#6366F1', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316', '#06B6D4'][$index] ?? '#6B7280' }}">
                                    {{ $index + 1 }}
                                </div>
                                <span class="text-sm font-medium">{{ $tipo->tipo_custo }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-semibold">R$ {{ number_format($tipo->total, 2, ',', '.') }}</span>
                                <span class="text-xs text-gray-500 ml-2">({{ $tipo->quantidade }} {{ $tipo->quantidade == 1 ? 'lançamento' : 'lançamentos' }})</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 ml-11 mb-3">
                            <div class="h-2 rounded-full" 
                                 style="width: {{ $despesasPorTipoCusto->max('total') > 0 ? ($tipo->total / $despesasPorTipoCusto->max('total') * 100) : 0 }}%; 
                                        background-color: {{ ['#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#6366F1', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316', '#06B6D4'][$index] ?? '#6B7280' }}">
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Nenhuma despesa por tipo de custo encontrada</p>
                @endif
            </div>
        </div>

        <!-- Próximos Vencimentos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Próximas Receitas -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4 text-green-600">Próximas Receitas (7 dias)</h2>
                @if($proximasReceitas->count() > 0)
                    <div class="space-y-3">
                        @foreach($proximasReceitas as $conta)
                        <div class="border-l-4 border-green-500 pl-4 py-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium">{{ $conta->descricao }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $conta->cliente ? $conta->cliente->nome : ($conta->cliente_nome ?? 'Sem cliente') }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Vence em {{ Carbon\Carbon::parse($conta->data_vencimento)->format('d/m/Y') }}
                                    </p>
                                </div>
                                <span class="font-bold text-green-600">R$ {{ number_format($conta->valor, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Nenhuma receita nos próximos 7 dias.</p>
                @endif
            </div>

            <!-- Próximas Despesas -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4 text-red-600">Próximas Despesas (7 dias)</h2>
                @if($proximasDespesas->count() > 0)
                    <div class="space-y-3">
                        @foreach($proximasDespesas as $conta)
                        <div class="border-l-4 border-red-500 pl-4 py-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium">{{ $conta->descricao }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $conta->fornecedor ? $conta->fornecedor->nome : 'Sem fornecedor' }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Vence em {{ Carbon\Carbon::parse($conta->data_vencimento)->format('d/m/Y') }}
                                    </p>
                                </div>
                                <span class="font-bold text-red-600">R$ {{ number_format($conta->valor, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Nenhuma despesa nos próximos 7 dias.</p>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($comparar)
        // Gráfico de Comparação
        const ctxComparacao = document.getElementById('graficoComparacao').getContext('2d');
        new Chart(ctxComparacao, {
            type: 'bar',
            data: {
                labels: {!! json_encode(collect($dadosComparacao['comparacao'])->pluck('unidade.nome')) !!},
                datasets: [
                    {
                        label: 'Receitas',
                        data: {!! json_encode(collect($dadosComparacao['comparacao'])->pluck('receitas')) !!},
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1
                    },
                    {
                        label: 'Despesas',
                        data: {!! json_encode(collect($dadosComparacao['comparacao'])->pluck('despesas')) !!},
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 1
                    },
                    {
                        label: 'Lucro',
                        data: {!! json_encode(collect($dadosComparacao['comparacao'])->pluck('lucro')) !!},
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    }
                }
            }
        });
    @else
        // Gráfico Fluxo Diário
        const ctxFluxo = document.getElementById('graficoFluxoDiario').getContext('2d');
        new Chart(ctxFluxo, {
            type: 'line',
            data: {
                labels: {!! json_encode(collect($fluxoDiario)->pluck('dia')) !!},
                datasets: [
                    {
                        label: 'Receitas',
                        data: {!! json_encode(collect($fluxoDiario)->pluck('receitas')) !!},
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Despesas',
                        data: {!! json_encode(collect($fluxoDiario)->pluck('despesas')) !!},
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    }
                }
            }
        });

        // Gráfico Evolução
        const ctxEvolucao = document.getElementById('graficoEvolucao').getContext('2d');
        new Chart(ctxEvolucao, {
            type: 'bar',
            data: {
                labels: {!! json_encode(collect($evolucao)->pluck('mes')) !!},
                datasets: [
                    {
                        label: 'Receitas',
                        data: {!! json_encode(collect($evolucao)->pluck('receitas')) !!},
                        backgroundColor: 'rgba(34, 197, 94, 0.8)'
                    },
                    {
                        label: 'Despesas',
                        data: {!! json_encode(collect($evolucao)->pluck('despesas')) !!},
                        backgroundColor: 'rgba(239, 68, 68, 0.8)'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    }
                }
            }
        });

        @if($despesasPorCategoria->count() > 0)
        // Gráfico Despesas por Categoria
        const ctxDespesas = document.getElementById('graficoDespesasCategoria').getContext('2d');
        new Chart(ctxDespesas, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($despesasPorCategoria->pluck('categoria')) !!},
                datasets: [{
                    data: {!! json_encode($despesasPorCategoria->pluck('total')) !!},
                    backgroundColor: {!! json_encode($despesasPorCategoria->pluck('cor')) !!}
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        @endif

        @if($receitasPorCategoria->count() > 0)
        // Gráfico Receitas por Categoria
        const ctxReceitas = document.getElementById('graficoReceitasCategoria').getContext('2d');
        new Chart(ctxReceitas, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($receitasPorCategoria->pluck('categoria')) !!},
                datasets: [{
                    data: {!! json_encode($receitasPorCategoria->pluck('total')) !!},
                    backgroundColor: {!! json_encode($receitasPorCategoria->pluck('cor')) !!}
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        @endif

        // Mini Gauge Margem Lucro
        const ctxGaugeMargem = document.getElementById('gaugeMargemLucro').getContext('2d');
        new Chart(ctxGaugeMargem, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [{{ $margemLucro }}, {{ 100 - $margemLucro }}],
                    backgroundColor: [
                        '{{ $margemLucro >= 20 ? "#22C55E" : ($margemLucro >= 10 ? "#EAB308" : "#EF4444") }}',
                        '#E5E7EB'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                }
            }
        });

        // Mini Gauge Inadimplência
        const ctxGaugeInadimplencia = document.getElementById('gaugeInadimplencia').getContext('2d');
        new Chart(ctxGaugeInadimplencia, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [{{ $inadimplencia }}, {{ 100 - $inadimplencia }}],
                    backgroundColor: [
                        '{{ $inadimplencia <= 5 ? "#22C55E" : ($inadimplencia <= 10 ? "#EAB308" : "#EF4444") }}',
                        '#E5E7EB'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                }
            }
        });
    @endif
});

// Função para abrir modal com categorias do tipo de custo
function abrirModalTipoCusto(tipoCusto, mes, ano, unidadeId) {
    document.getElementById('modalTipoCustoTitulo').textContent = 'Categorias - ' + tipoCusto;
    document.getElementById('modalTipoCustoCorpo').innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i><p class="mt-2 text-gray-500">Carregando...</p></div>';
    document.getElementById('modalTipoCusto').classList.remove('hidden');
    
    // Fazer requisição AJAX para buscar categorias
    fetch(`/admin/dashboard-financeira/categorias-por-tipo-custo?tipo_custo=${encodeURIComponent(tipoCusto)}&mes=${mes}&ano=${ano}&unidade_id=${unidadeId}`)
        .then(response => response.json())
        .then(data => {
            let html = '';
            if (data.length > 0) {
                html = '<div class="space-y-3">';
                data.forEach((categoria, index) => {
                    const cor = categoria.cor || ['#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#6366F1', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316', '#06B6D4'][index % 10];
                    html += `
                        <div class="flex items-center justify-between mb-2 cursor-pointer hover:bg-gray-50 p-3 rounded transition-colors border border-gray-200"
                             onclick="abrirModalLancamentosCategoria(${categoria.categoria_id}, '${categoria.categoria_nome}', ${mes}, ${ano}, ${unidadeId})">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-3" style="background-color: ${cor}"></div>
                                <span class="text-sm font-medium">${categoria.categoria_nome}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-semibold">R$ ${parseFloat(categoria.total).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                <span class="text-xs text-gray-500 ml-2">(${categoria.quantidade} ${categoria.quantidade == 1 ? 'lançamento' : 'lançamentos'})</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 ml-6 mb-3">
                            <div class="h-2 rounded-full" 
                                 style="width: ${data[0].total > 0 ? (categoria.total / data[0].total * 100) : 0}%; 
                                        background-color: ${cor}">
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            } else {
                html = '<p class="text-center text-gray-500 py-4">Nenhuma categoria encontrada para este tipo de custo.</p>';
            }
            document.getElementById('modalTipoCustoCorpo').innerHTML = html;
        })
        .catch(error => {
            console.error('Erro:', error);
            document.getElementById('modalTipoCustoCorpo').innerHTML = '<p class="text-center text-red-500 py-4">Erro ao carregar os dados.</p>';
        });
}

// Função para abrir modal com lançamentos da categoria
function abrirModalLancamentosCategoria(categoriaId, categoriaNome, mes, ano, unidadeId) {
    document.getElementById('modalTipoCustoTitulo').textContent = 'Lançamentos - ' + categoriaNome;
    document.getElementById('modalTipoCustoCorpo').innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i><p class="mt-2 text-gray-500">Carregando...</p></div>';
    
    // Fazer requisição AJAX para buscar lançamentos da categoria
    fetch(`/admin/dashboard-financeira/lancamentos-por-categoria?categoria_id=${categoriaId}&mes=${mes}&ano=${ano}&unidade_id=${unidadeId}`)
        .then(response => response.json())
        .then(data => {
            let html = '';
            if (data.length > 0) {
                html = '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200">';
                html += '<thead class="bg-gray-50"><tr>';
                html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>';
                html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>';
                html += '<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Valor</th>';
                html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>';
                html += '</tr></thead><tbody class="bg-white divide-y divide-gray-200">';
                
                data.forEach(lancamento => {
                    const statusColor = lancamento.status === 'Pago' ? 'text-green-600' : 
                                       lancamento.status === 'Pendente' ? 'text-yellow-600' : 'text-red-600';
                    html += `<tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">${lancamento.data_vencimento}</td>
                        <td class="px-6 py-4 text-sm">${lancamento.descricao}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">R$ ${lancamento.valor}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm ${statusColor}">${lancamento.status}</td>
                    </tr>`;
                });
                html += '</tbody></table></div>';
                
                // Adicionar botão para voltar às categorias
                html += `
                    <div class="mt-4 pt-4 border-t">
                        <button onclick="abrirModalTipoCusto('${document.getElementById('modalTipoCustoTitulo').textContent.replace('Lançamentos - ', '').replace('Categorias - ', '')}', ${mes}, ${ano}, ${unidadeId})" 
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>Voltar para categorias
                        </button>
                    </div>
                `;
            } else {
                html = '<p class="text-center text-gray-500 py-4">Nenhum lançamento encontrado para esta categoria.</p>';
            }
            document.getElementById('modalTipoCustoCorpo').innerHTML = html;
        })
        .catch(error => {
            console.error('Erro:', error);
            document.getElementById('modalTipoCustoCorpo').innerHTML = '<p class="text-center text-red-500 py-4">Erro ao carregar os dados.</p>';
        });
}

function fecharModalTipoCusto() {
    document.getElementById('modalTipoCusto').classList.add('hidden');
}

// Função para abrir modal com receitas por categoria
function abrirModalReceita(categoriaId, categoriaNome, mes, ano, unidadeId) {
    document.getElementById('modalReceitaTitulo').textContent = 'Receitas - ' + categoriaNome;
    document.getElementById('modalReceitaCorpo').innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i><p class="mt-2 text-gray-500">Carregando...</p></div>';
    document.getElementById('modalReceita').classList.remove('hidden');
    
    // Fazer requisição AJAX para buscar receitas da categoria
    fetch(`/admin/dashboard-financeira/receitas-por-categoria?categoria_id=${categoriaId}&mes=${mes}&ano=${ano}&unidade_id=${unidadeId}`)
        .then(response => response.json())
        .then(data => {
            let html = '';
            if (data.length > 0) {
                html = '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200">';
                html += '<thead class="bg-gray-50"><tr>';
                html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>';
                html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>';
                html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>';
                html += '<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Valor</th>';
                html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>';
                html += '</tr></thead><tbody class="bg-white divide-y divide-gray-200">';
                
                data.forEach(lancamento => {
                    const statusColor = lancamento.status === 'Recebido' ? 'text-green-600' : 
                                       lancamento.status === 'Pendente' ? 'text-yellow-600' : 'text-red-600';
                    html += `<tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">${lancamento.data_vencimento}</td>
                        <td class="px-6 py-4 text-sm">${lancamento.descricao}</td>
                        <td class="px-6 py-4 text-sm">${lancamento.cliente}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">R$ ${lancamento.valor}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm ${statusColor}">${lancamento.status}</td>
                    </tr>`;
                });
                html += '</tbody></table></div>';
            } else {
                html = '<p class="text-center text-gray-500 py-4">Nenhuma receita encontrada para esta categoria.</p>';
            }
            document.getElementById('modalReceitaCorpo').innerHTML = html;
        })
        .catch(error => {
            console.error('Erro:', error);
            document.getElementById('modalReceitaCorpo').innerHTML = '<p class="text-center text-red-500 py-4">Erro ao carregar os dados.</p>';
        });
}

function fecharModalReceita() {
    document.getElementById('modalReceita').classList.add('hidden');
}
</script>

<!-- Modal Lançamentos por Tipo de Custo -->
<div id="modalTipoCusto" class="fixed z-50 inset-0 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 id="modalTipoCustoTitulo" class="text-lg leading-6 font-medium text-gray-900">Lançamentos</h3>
                    <button onclick="fecharModalTipoCusto()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="modalTipoCustoCorpo">
                    <!-- Conteúdo carregado via AJAX -->
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="fecharModalTipoCusto()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Receitas por Categoria -->
<div id="modalReceita" class="fixed z-50 inset-0 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 id="modalReceitaTitulo" class="text-lg leading-6 font-medium text-gray-900">Receitas</h3>
                    <button onclick="fecharModalReceita()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="modalReceitaCorpo">
                    <!-- Conteúdo carregado via AJAX -->
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="fecharModalReceita()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>
@endpush