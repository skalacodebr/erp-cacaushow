@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Campanha: {{ $campanha->nome }}</h1>
        <div>
            @if($campanha->status == 'rascunho' || $campanha->status == 'agendada')
                <form action="{{ route('admin.campanhas.enviar', $campanha) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success" 
                            onclick="return confirm('Tem certeza que deseja enviar esta campanha agora?');">
                        <i class="fas fa-paper-plane"></i> Enviar Agora
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.campanhas.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
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
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Status</h6>
                    @php
                        $statusColors = [
                            'rascunho' => 'secondary',
                            'agendada' => 'warning',
                            'enviando' => 'info',
                            'enviada' => 'success',
                            'pausada' => 'danger'
                        ];
                    @endphp
                    <h4 class="card-title">
                        <span class="badge bg-{{ $statusColors[$campanha->status] ?? 'secondary' }}">
                            {{ ucfirst($campanha->status) }}
                        </span>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Lista</h6>
                    <h5 class="card-title">{{ $campanha->lista->nome }}</h5>
                    <small class="text-muted">{{ $campanha->lista->total_contatos }} contatos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Emails Enviados</h6>
                    <h4 class="card-title">{{ $campanha->total_enviados }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Data de Envio</h6>
                    <h6 class="card-title">
                        {{ $campanha->data_envio ? $campanha->data_envio->format('d/m/Y H:i') : 'Não enviada' }}
                    </h6>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Informações da Campanha</h5>
        </div>
        <div class="card-body">
            @if($campanha->descricao)
                <div class="mb-3">
                    <strong>Descrição:</strong><br>
                    {{ $campanha->descricao }}
                </div>
            @endif
            
            <div class="mb-3">
                <strong>Assunto do Email:</strong><br>
                {{ $campanha->assunto }}
            </div>
            
            <div class="mb-3">
                <strong>Conteúdo:</strong><br>
                <div class="border p-3 bg-light">
                    {!! nl2br(e($campanha->conteudo)) !!}
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <strong>Criada em:</strong> {{ $campanha->created_at->format('d/m/Y H:i') }}
                </div>
                <div class="col-md-6">
                    <strong>Última atualização:</strong> {{ $campanha->updated_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
    </div>
    
    @if($campanha->status == 'enviada')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Estatísticas de Envio</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center">
                        <h3>{{ $campanha->total_enviados }}</h3>
                        <p class="text-muted">Emails Enviados</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h3>{{ $campanha->total_abertos }}</h3>
                        <p class="text-muted">Emails Abertos</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h3>{{ $campanha->total_cliques }}</h3>
                        <p class="text-muted">Cliques</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection