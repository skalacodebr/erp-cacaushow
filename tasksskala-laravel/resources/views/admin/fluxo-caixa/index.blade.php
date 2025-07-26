@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Alertas de Contas Atrasadas -->
    @if($contasPagarAtrasadas->count() > 0 || $contasReceberAtrasadas->count() > 0)
        <div class="mb-6 space-y-4">
            @if($contasPagarAtrasadas->count() > 0)
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <p class="font-bold">Atenção! Você tem {{ $contasPagarAtrasadas->count() }} conta(s) a pagar em atraso</p>
                    </div>
                    <p class="mt-2">Valor total em atraso: R$ {{ number_format($contasPagarAtrasadas->sum('valor'), 2, ',', '.') }}</p>
                    <a href="{{ route('admin.contas-pagar.index', ['status' => 'vencido']) }}" class="text-red-600 hover:text-red-800 underline mt-2 inline-block">Ver contas atrasadas</a>
                </div>
            @endif
            
            @if($contasReceberAtrasadas->count() > 0)
                <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <p class="font-bold">Atenção! Você tem {{ $contasReceberAtrasadas->count() }} conta(s) a receber em atraso</p>
                    </div>
                    <p class="mt-2">Valor total em atraso: R$ {{ number_format($contasReceberAtrasadas->sum('valor'), 2, ',', '.') }}</p>
                    <a href="{{ route('admin.contas-receber.index', ['status' => 'vencido']) }}" class="text-orange-600 hover:text-orange-800 underline mt-2 inline-block">Ver contas atrasadas</a>
                </div>
            @endif
        </div>
    @endif

    <!-- Cabeçalho -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Fluxo de Caixa</h1>
        
        <!-- Filtros -->
        <form method="GET" action="{{ route('admin.fluxo-caixa.index') }}" class="flex space-x-2">
            <select name="mes" class="rounded-md border-gray-300" onchange="this.form.submit()">
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $mesAtual == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($i)->locale('pt_BR')->monthName }}
                    </option>
                @endfor
            </select>
            <select name="ano" class="rounded-md border-gray-300" onchange="this.form.submit()">
                @for($i = date('Y') - 2; $i <= date('Y') + 2; $i++)
                    <option value="{{ $i }}" {{ $anoAtual == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </form>
    </div>

    <!-- Cards de Resumo -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Saldo Inicial -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Saldo Inicial</p>
                    <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($saldoInicial, 2, ',', '.') }}</p>
                </div>
                <i class="fas fa-wallet text-3xl text-blue-500"></i>
            </div>
        </div>

        <!-- Total a Receber -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">A Receber</p>
                    <p class="text-2xl font-bold text-green-600">R$ {{ number_format($totalReceber, 2, ',', '.') }}</p>
                    <p class="text-xs text-gray-500">Recebido: R$ {{ number_format($totalRecebido, 2, ',', '.') }}</p>
                </div>
                <i class="fas fa-arrow-down text-3xl text-green-500"></i>
            </div>
        </div>

        <!-- Total a Pagar -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">A Pagar</p>
                    <p class="text-2xl font-bold text-red-600">R$ {{ number_format($totalPagar, 2, ',', '.') }}</p>
                    <p class="text-xs text-gray-500">Pago: R$ {{ number_format($totalPago, 2, ',', '.') }}</p>
                </div>
                <i class="fas fa-arrow-up text-3xl text-red-500"></i>
            </div>
        </div>

        <!-- Saldo Previsto -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Saldo Previsto</p>
                    <p class="text-2xl font-bold {{ $saldoPrevisto >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        R$ {{ number_format($saldoPrevisto, 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500">Atual: R$ {{ number_format($saldoAtual, 2, ',', '.') }}</p>
                </div>
                <i class="fas fa-chart-line text-3xl {{ $saldoPrevisto >= 0 ? 'text-green-500' : 'text-red-500' }}"></i>
            </div>
        </div>
    </div>

    <!-- Fluxo Diário -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-lg font-semibold">Fluxo Diário - {{ \Carbon\Carbon::create()->month($mesAtual)->locale('pt_BR')->monthName }} {{ $anoAtual }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dia</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Entradas</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Saídas</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo do Dia</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $saldoAcumulado = $saldoInicial; @endphp
                    @foreach($fluxoDiario as $dia)
                        @php 
                            $temMovimento = $dia['receber'] > 0 || $dia['pagar'] > 0;
                            $saldoDia = $dia['receber'] - $dia['pagar'];
                            $saldoAcumulado += $saldoDia;
                        @endphp
                        @if($temMovimento)
                            <tr class="{{ $dia['data']->isToday() ? 'bg-blue-50' : '' }} {{ $dia['data']->isWeekend() ? 'bg-gray-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm {{ $dia['data']->isToday() ? 'font-bold' : '' }}">
                                    {{ $dia['data']->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $dia['data']->locale('pt_BR')->dayName }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    @if($dia['receber'] > 0)
                                        <span class="text-green-600 font-semibold">
                                            + R$ {{ number_format($dia['receber'], 2, ',', '.') }}
                                        </span>
                                        <br>
                                        <span class="text-xs text-gray-500">({{ $dia['contas_receber']->count() }} conta{{ $dia['contas_receber']->count() > 1 ? 's' : '' }})</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    @if($dia['pagar'] > 0)
                                        <span class="text-red-600 font-semibold">
                                            - R$ {{ number_format($dia['pagar'], 2, ',', '.') }}
                                        </span>
                                        <br>
                                        <span class="text-xs text-gray-500">({{ $dia['contas_pagar']->count() }} conta{{ $dia['contas_pagar']->count() > 1 ? 's' : '' }})</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold {{ $saldoDia >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    R$ {{ number_format($saldoDia, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <button onclick="mostrarDetalhes('{{ $dia['data']->format('Y-m-d') }}')" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-eye"></i> Ver detalhes
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Linha de detalhes (inicialmente oculta) -->
                            <tr id="detalhes-{{ $dia['data']->format('Y-m-d') }}" class="hidden">
                                <td colspan="6" class="px-6 py-4 bg-gray-50">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Contas a Receber do dia -->
                                        @if($dia['contas_receber']->count() > 0)
                                            <div>
                                                <h4 class="font-semibold text-green-600 mb-2">Contas a Receber</h4>
                                                <ul class="space-y-1">
                                                    @foreach($dia['contas_receber'] as $conta)
                                                        <li class="flex justify-between items-center p-2 bg-white rounded">
                                                            <div>
                                                                <span class="text-sm">{{ $conta->descricao }}</span>
                                                                @if($conta->cliente)
                                                                    <span class="text-xs text-gray-500 block">{{ $conta->cliente->nome }}</span>
                                                                @endif
                                                            </div>
                                                            <div class="text-right">
                                                                <span class="text-sm font-semibold">R$ {{ number_format($conta->valor, 2, ',', '.') }}</span>
                                                                <span class="text-xs block px-2 inline-flex leading-5 font-semibold rounded-full 
                                                                    {{ $conta->status == 'recebido' ? 'bg-green-100 text-green-800' : '' }}
                                                                    {{ $conta->status == 'pendente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                                    {{ $conta->status == 'vencido' ? 'bg-red-100 text-red-800' : '' }}">
                                                                    {{ ucfirst($conta->status) }}
                                                                </span>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        
                                        <!-- Contas a Pagar do dia -->
                                        @if($dia['contas_pagar']->count() > 0)
                                            <div>
                                                <h4 class="font-semibold text-red-600 mb-2">Contas a Pagar</h4>
                                                <ul class="space-y-1">
                                                    @foreach($dia['contas_pagar'] as $conta)
                                                        <li class="flex justify-between items-center p-2 bg-white rounded">
                                                            <div>
                                                                <span class="text-sm">{{ $conta->descricao }}</span>
                                                                @if($conta->fornecedor)
                                                                    <span class="text-xs text-gray-500 block">{{ $conta->fornecedor }}</span>
                                                                @endif
                                                            </div>
                                                            <div class="text-right">
                                                                <span class="text-sm font-semibold">R$ {{ number_format($conta->valor, 2, ',', '.') }}</span>
                                                                <span class="text-xs block px-2 inline-flex leading-5 font-semibold rounded-full 
                                                                    {{ $conta->status == 'pago' ? 'bg-green-100 text-green-800' : '' }}
                                                                    {{ $conta->status == 'pendente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                                    {{ $conta->status == 'vencido' ? 'bg-red-100 text-red-800' : '' }}">
                                                                    {{ ucfirst($conta->status) }}
                                                                </span>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-sm font-semibold">Totais do Mês</td>
                        <td class="px-6 py-4 text-right text-sm font-semibold text-green-600">
                            + R$ {{ number_format($contasReceber->sum('valor'), 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-semibold text-red-600">
                            - R$ {{ number_format($contasPagar->sum('valor'), 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-semibold {{ ($contasReceber->sum('valor') - $contasPagar->sum('valor')) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            R$ {{ number_format($contasReceber->sum('valor') - $contasPagar->sum('valor'), 2, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Resumo por Conta Bancária -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($contasBancarias as $conta)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-lg mb-4">{{ $conta->nome }}</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Banco:</span>
                        <span class="font-medium">{{ $conta->banco }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Conta:</span>
                        <span class="font-medium">{{ $conta->conta }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Saldo Atual:</span>
                        <span class="font-bold {{ $conta->saldo_atual >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            R$ {{ number_format($conta->saldo_atual, 2, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
function mostrarDetalhes(data) {
    const detalhes = document.getElementById('detalhes-' + data);
    if (detalhes.classList.contains('hidden')) {
        detalhes.classList.remove('hidden');
    } else {
        detalhes.classList.add('hidden');
    }
}
</script>
@endsection