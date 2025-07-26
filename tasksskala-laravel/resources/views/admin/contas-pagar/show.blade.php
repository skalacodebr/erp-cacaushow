@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Detalhes da Conta a Pagar</h1>
            <div>
                @if($conta->status == 'pendente')
                    <button onclick="abrirModalPagar()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2">
                        Pagar
                    </button>
                @endif
                <a href="{{ route('admin.contas-pagar.edit', $conta->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Editar
                </a>
                <a href="{{ route('admin.contas-pagar.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Voltar
                </a>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ $conta->descricao }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Informações detalhadas da conta a pagar
                </p>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Valor</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="text-lg font-semibold">R$ {{ number_format($conta->valor, 2, ',', '.') }}</span>
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $conta->status == 'pago' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $conta->status == 'pendente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $conta->status == 'vencido' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $conta->status == 'cancelado' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ ucfirst($conta->status) }}
                            </span>
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Data de Vencimento</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $conta->data_vencimento->format('d/m/Y') }}
                            @if($conta->status == 'pendente' && $conta->data_vencimento < now())
                                <span class="text-red-600 text-xs ml-2">(Vencida há {{ $conta->data_vencimento->diffInDays(now()) }} dias)</span>
                            @endif
                        </dd>
                    </div>
                    @if($conta->data_pagamento)
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Data de Pagamento</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $conta->data_pagamento->format('d/m/Y') }}</dd>
                    </div>
                    @endif
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ ucfirst($conta->tipo) }}
                            @if($conta->parcela_atual && $conta->total_parcelas)
                                - Parcela {{ $conta->parcela_atual }}/{{ $conta->total_parcelas }}
                            @endif
                            @if($conta->periodicidade)
                                - {{ ucfirst($conta->periodicidade) }}
                            @endif
                        </dd>
                    </div>
                    @if($conta->fornecedor)
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Fornecedor</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $conta->fornecedor }}</dd>
                    </div>
                    @endif
                    @if($conta->categoria)
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Categoria</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $conta->categoria }}</dd>
                    </div>
                    @endif
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Conta Bancária</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if($conta->contaBancaria)
                                {{ $conta->contaBancaria->nome }} - {{ $conta->contaBancaria->banco }}
                            @else
                                <span class="text-gray-400">Não definida</span>
                            @endif
                        </dd>
                    </div>
                    @if($conta->observacoes)
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Observações</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $conta->observacoes }}</dd>
                    </div>
                    @endif
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Criado em</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $conta->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Última atualização</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $conta->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pagar Conta -->
@if($conta->status == 'pendente')
<div id="modalPagar" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.contas-pagar.pagar', $conta->id) }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Confirmar Pagamento</h3>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Data do Pagamento</label>
                        <input type="date" name="data_pagamento" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Conta Bancária</label>
                        <select name="conta_bancaria_id" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Selecione uma conta</option>
                            @foreach(\App\Models\ContaBancaria::where('ativo', true)->get() as $contaBancaria)
                                <option value="{{ $contaBancaria->id }}" {{ $conta->conta_bancaria_id == $contaBancaria->id ? 'selected' : '' }}>
                                    {{ $contaBancaria->nome }} - {{ $contaBancaria->banco }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirmar Pagamento
                    </button>
                    <button type="button" onclick="fecharModalPagar()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModalPagar() {
    document.getElementById('modalPagar').classList.remove('hidden');
}

function fecharModalPagar() {
    document.getElementById('modalPagar').classList.add('hidden');
}
</script>
@endif
@endsection