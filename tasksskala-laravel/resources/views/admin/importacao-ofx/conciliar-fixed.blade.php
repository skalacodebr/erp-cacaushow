@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Transações Pendentes de Conciliação</h1>
                <div class="flex gap-2">
                    <a href="{{ route('admin.importacao-ofx.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-upload mr-2"></i>Nova Importação
                    </a>
                    <a href="{{ route('admin.importacao-ofx.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-list mr-2"></i>Todas as Transações
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if($transacoesPendentes->isEmpty())
                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded">
                    <i class="fas fa-info-circle mr-2"></i>Não há transações pendentes de conciliação.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Beneficiário</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conta Bancária</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($transacoesPendentes as $transacao)
                            <tr id="transacao-{{ $transacao->id }}" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $transacao->data_transacao->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $transacao->descricao }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $transacao->beneficiario }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transacao->tipo_conta == 'pagar' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        R$ {{ number_format($transacao->valor, 2, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transacao->tipo_conta == 'pagar' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $transacao->tipo_conta == 'pagar' ? 'A Pagar' : 'A Receber' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $transacao->conta_bancaria }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs mr-2" 
                                            onclick="abrirModal({{ $transacao->id }})">
                                        <i class="fas fa-link"></i> Conciliar
                                    </button>
                                    <button class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-3 rounded text-xs" 
                                            onclick="ignorar({{ $transacao->id }})">
                                        <i class="fas fa-eye-slash"></i> Ignorar
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $transacoesPendentes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Simplificado -->
<div id="modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;">
    <div style="background:white;margin:50px auto;padding:20px;width:90%;max-width:800px;border-radius:8px;">
        <div style="display:flex;justify-content:space-between;margin-bottom:20px;">
            <h2 style="font-size:1.5rem;font-weight:bold;">Conciliar Transação</h2>
            <button onclick="fecharModal()" style="font-size:1.5rem;cursor:pointer;">&times;</button>
        </div>
        
        <div id="detalhes" style="background:#f5f5f5;padding:10px;margin-bottom:20px;border-radius:4px;">
            <!-- Detalhes serão inseridos aqui -->
        </div>
        
        <div style="margin-bottom:20px;">
            <h3 style="font-weight:bold;margin-bottom:10px;">Contas Sugeridas:</h3>
            <div id="sugestoes" style="max-height:200px;overflow-y:auto;">
                <!-- Sugestões serão inseridas aqui -->
            </div>
        </div>
        
        <div style="margin-bottom:20px;">
            <label style="display:block;margin-bottom:5px;">Selecione uma ação:</label>
            <select id="acao" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
                <option value="">Escolha...</option>
                <option value="criar">Criar Nova Conta</option>
                <option value="vincular">Vincular a Conta Existente</option>
            </select>
            
            <div id="conta-select" style="display:none;margin-top:10px;">
                <label style="display:block;margin-bottom:5px;">Selecione a conta:</label>
                <select id="conta_id" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
                    <option value="">Selecione...</option>
                </select>
            </div>
        </div>
        
        <div style="display:flex;justify-content:flex-end;gap:10px;">
            <button onclick="fecharModal()" style="padding:8px 16px;background:#ccc;color:white;border:none;border-radius:4px;cursor:pointer;">
                Cancelar
            </button>
            <button onclick="confirmar()" style="padding:8px 16px;background:#3B82F6;color:white;border:none;border-radius:4px;cursor:pointer;">
                Confirmar
            </button>
        </div>
    </div>
</div>

<script>
let transacaoAtual = null;

function abrirModal(id) {
    console.log('Abrindo modal para transação:', id);
    transacaoAtual = id;
    
    // Mostra o modal
    document.getElementById('modal').style.display = 'block';
    
    // Busca dados da transação
    const linha = document.getElementById('transacao-' + id);
    if (linha) {
        const colunas = linha.getElementsByTagName('td');
        const detalhes = `
            <strong>Data:</strong> ${colunas[0].innerText}<br>
            <strong>Descrição:</strong> ${colunas[1].innerText}<br>
            <strong>Beneficiário:</strong> ${colunas[2].innerText}<br>
            <strong>Valor:</strong> ${colunas[3].innerText}<br>
            <strong>Tipo:</strong> ${colunas[4].innerText}
        `;
        document.getElementById('detalhes').innerHTML = detalhes;
    }
    
    // Busca contas sugeridas
    fetch('/admin/importacao-ofx/buscar-contas/' + id)
        .then(r => r.json())
        .then(contas => {
            console.log('Contas recebidas:', contas);
            const div = document.getElementById('sugestoes');
            
            if (contas && contas.length > 0) {
                div.innerHTML = contas.map(c => `
                    <div style="border:1px solid #ddd;padding:10px;margin-bottom:5px;border-radius:4px;">
                        <strong>${c.descricao}</strong><br>
                        <small>Valor: R$ ${parseFloat(c.valor).toFixed(2).replace('.',',')}</small>
                        <button onclick="selecionarConta(${c.id})" style="float:right;padding:4px 8px;background:#10B981;color:white;border:none;border-radius:4px;cursor:pointer;">
                            Selecionar
                        </button>
                    </div>
                `).join('');
            } else {
                div.innerHTML = '<p>Nenhuma conta sugerida encontrada.</p>';
            }
        })
        .catch(e => {
            console.error('Erro:', e);
            document.getElementById('sugestoes').innerHTML = '<p style="color:red;">Erro ao buscar contas.</p>';
        });
}

function fecharModal() {
    document.getElementById('modal').style.display = 'none';
    document.getElementById('acao').value = '';
    document.getElementById('conta-select').style.display = 'none';
}

function selecionarConta(id) {
    document.getElementById('acao').value = 'vincular';
    document.getElementById('conta-select').style.display = 'block';
    document.getElementById('conta_id').value = id;
}

function ignorar(id) {
    if (confirm('Deseja realmente ignorar esta transação?')) {
        enviarAcao(id, 'ignorar', null);
    }
}

function confirmar() {
    const acao = document.getElementById('acao').value;
    if (!acao) {
        alert('Selecione uma ação');
        return;
    }
    
    let contaId = null;
    if (acao === 'vincular') {
        contaId = document.getElementById('conta_id').value;
        if (!contaId) {
            alert('Selecione uma conta');
            return;
        }
    }
    
    enviarAcao(transacaoAtual, acao, contaId);
}

function enviarAcao(transacaoId, acao, contaId) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    
    fetch('/admin/importacao-ofx/conciliar/' + transacaoId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({
            acao: acao,
            conta_id: contaId,
            _token: token
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const linha = document.getElementById('transacao-' + transacaoId);
            if (linha) linha.remove();
            fecharModal();
            alert('Transação processada com sucesso!');
            
            if (document.querySelectorAll('tbody tr').length === 0) {
                location.reload();
            }
        } else {
            alert('Erro: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(e => {
        console.error('Erro:', e);
        alert('Erro ao processar transação');
    });
}

// Evento para mudar ação
document.getElementById('acao')?.addEventListener('change', function() {
    document.getElementById('conta-select').style.display = 
        this.value === 'vincular' ? 'block' : 'none';
});

// Fechar modal ao clicar fora
document.getElementById('modal')?.addEventListener('click', function(e) {
    if (e.target === this) fecharModal();
});
</script>

@endsection