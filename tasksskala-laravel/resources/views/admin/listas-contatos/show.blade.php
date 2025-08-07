@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Lista: {{ $listaContato->nome }}</h1>
        <a href="{{ route('admin.listas-contatos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Total de Contatos</h6>
                    <h3 class="card-title">{{ $listaContato->total_contatos }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Status</h6>
                    <h3 class="card-title">
                        <span class="badge bg-{{ $listaContato->status == 'ativa' ? 'success' : 'secondary' }}">
                            {{ ucfirst($listaContato->status) }}
                        </span>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Criada em</h6>
                    <h5 class="card-title">{{ $listaContato->created_at->format('d/m/Y H:i') }}</h5>
                </div>
            </div>
        </div>
    </div>
    
    @if($listaContato->descricao)
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">Descrição</h6>
            <p class="card-text">{{ $listaContato->descricao }}</p>
        </div>
    </div>
    @endif
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Contatos da Lista</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Empresa</th>
                            <th>Cargo</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contatos as $contato)
                            <tr>
                                <td>{{ $contato->nome }}</td>
                                <td>{{ $contato->email }}</td>
                                <td>{{ $contato->telefone ?: '-' }}</td>
                                <td>{{ $contato->empresa ?: '-' }}</td>
                                <td>{{ $contato->cargo ?: '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $contato->status == 'ativo' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($contato->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Nenhum contato nesta lista</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $contatos->links() }}
        </div>
    </div>
</div>
@endsection