@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Contas a Receber</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.importacao-ofx.create') }}?tipo_conta=receber" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-file-import mr-2"></i>Importar OFX
            </a>
            <a href="{{ route('admin.contas-receber.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Nova Conta
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form method="GET" action="{{ route('admin.contas-receber.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full rounded-md border-gray-300">
                    <option value="">Todos</option>
                    <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="recebido" {{ request('status') == 'recebido' ? 'selected' : '' }}>Recebido</option>
                    <option value="vencido" {{ request('status') == 'vencido' ? 'selected' : '' }}>Vencido</option>
                    <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mês</label>
                <select name="mes" class="w-full rounded-md border-gray-300">
                    <option value="">Todos</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('mes') == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->locale('pt_BR')->monthName }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ano</label>
                <select name="ano" class="w-full rounded-md border-gray-300">
                    <option value="">Todos</option>
                    @for($i = date('Y') - 2; $i <= date('Y') + 2; $i++)
                        <option value="{{ $i }}" {{ request('ano') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimento</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conta Bancária</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($contas as $conta)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $conta->descricao }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($conta->cliente_nome)
                                {{ $conta->cliente_nome }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            R$ {{ number_format($conta->valor, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $conta->data_vencimento->format('d/m/Y') }}
                            @if($conta->status == 'pendente' && $conta->data_vencimento < now())
                                <br><span class="text-xs text-red-600">Vencida</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $conta->status == 'recebido' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $conta->status == 'pendente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $conta->status == 'vencido' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $conta->status == 'cancelado' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ ucfirst($conta->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ ucfirst($conta->tipo) }}</span>
                            @if($conta->parcela_atual && $conta->total_parcelas)
                                <br><span class="text-xs text-gray-500">{{ $conta->parcela_atual }}/{{ $conta->total_parcelas }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $conta->contaBancaria->nome ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.contas-receber.show', $conta->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Ver</a>
                            <a href="{{ route('admin.contas-receber.edit', $conta->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Editar</a>
                            @if($conta->status == 'pendente')
                                <button onclick="abrirModalReceber({{ $conta->id }})" class="text-green-600 hover:text-green-900 mr-3">Receber</button>
                            @endif
                            <form action="{{ route('admin.contas-receber.destroy', $conta->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Tem certeza que deseja excluir esta conta?')">
                                    Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            Nenhuma conta a receber encontrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $contas->links() }}
    </div>
</div>

<!-- Modal Receber Conta -->
<div id="modalReceber" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="formReceber" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Confirmar Recebimento</h3>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Data do Recebimento</label>
                        <input type="date" name="data_recebimento" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Conta Bancária</label>
                        <select name="conta_bancaria_id" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Selecione uma conta</option>
                            @foreach(\App\Models\ContaBancaria::where('ativo', true)->get() as $contaBancaria)
                                <option value="{{ $contaBancaria->id }}">{{ $contaBancaria->nome }} - {{ $contaBancaria->banco }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirmar Recebimento
                    </button>
                    <button type="button" onclick="fecharModalReceber()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModalReceber(contaId) {
    document.getElementById('formReceber').action = `/admin/contas-receber/${contaId}/receber`;
    document.getElementById('modalReceber').classList.remove('hidden');
}

function fecharModalReceber() {
    document.getElementById('modalReceber').classList.add('hidden');
}
</script>
@endsection