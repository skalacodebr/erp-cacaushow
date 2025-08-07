@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Campanhas</h1>
        <a href="{{ route('admin.campanhas.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nova Campanha
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Lista</th>
                            <th>Status</th>
                            <th>Enviados</th>
                            <th>Data Envio</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campanhas as $campanha)
                            <tr>
                                <td>{{ $campanha->nome }}</td>
                                <td>{{ $campanha->lista->nome }}</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'rascunho' => 'secondary',
                                            'agendada' => 'warning',
                                            'enviando' => 'info',
                                            'enviada' => 'success',
                                            'pausada' => 'danger'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$campanha->status] ?? 'secondary' }}">
                                        {{ ucfirst($campanha->status) }}
                                    </span>
                                </td>
                                <td>{{ $campanha->total_enviados }}</td>
                                <td>
                                    {{ $campanha->data_envio ? $campanha->data_envio->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.campanhas.show', $campanha) }}" 
                                           class="btn btn-sm btn-info" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($campanha->status == 'rascunho')
                                            <a href="{{ route('admin.campanhas.edit', $campanha) }}" 
                                               class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.campanhas.destroy', $campanha) }}" 
                                                  method="POST" style="display: inline-block;"
                                                  onsubmit="return confirm('Tem certeza que deseja excluir esta campanha?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Nenhuma campanha encontrada</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $campanhas->links() }}
        </div>
    </div>
</div>
@endsection