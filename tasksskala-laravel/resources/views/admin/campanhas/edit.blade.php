@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Editar Campanha: {{ $campanha->nome }}</h1>
        <a href="{{ route('admin.campanhas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.campanhas.update', $campanha) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome da Campanha *</label>
                    <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                           id="nome" name="nome" value="{{ old('nome', $campanha->nome) }}" required>
                    @error('nome')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control @error('descricao') is-invalid @enderror" 
                              id="descricao" name="descricao" rows="3">{{ old('descricao', $campanha->descricao) }}</textarea>
                    @error('descricao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="lista_id" class="form-label">Lista de Contatos *</label>
                    <select class="form-select @error('lista_id') is-invalid @enderror" 
                            id="lista_id" name="lista_id" required>
                        <option value="">Selecione uma lista...</option>
                        @foreach($listas as $lista)
                            <option value="{{ $lista->id }}" {{ old('lista_id', $campanha->lista_id) == $lista->id ? 'selected' : '' }}>
                                {{ $lista->nome }} ({{ $lista->total_contatos }} contatos)
                            </option>
                        @endforeach
                    </select>
                    @error('lista_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="assunto" class="form-label">Assunto do Email *</label>
                    <input type="text" class="form-control @error('assunto') is-invalid @enderror" 
                           id="assunto" name="assunto" value="{{ old('assunto', $campanha->assunto) }}" required>
                    @error('assunto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="conteudo" class="form-label">Conteúdo do Email *</label>
                    <textarea class="form-control @error('conteudo') is-invalid @enderror" 
                              id="conteudo" name="conteudo" rows="10" required>{{ old('conteudo', $campanha->conteudo) }}</textarea>
                    @error('conteudo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="data_envio" class="form-label">Agendar Envio (Opcional)</label>
                    <input type="datetime-local" class="form-control @error('data_envio') is-invalid @enderror" 
                           id="data_envio" name="data_envio" 
                           value="{{ old('data_envio', $campanha->data_envio ? $campanha->data_envio->format('Y-m-d\TH:i') : '') }}">
                    @error('data_envio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                    <a href="{{ route('admin.campanhas.show', $campanha) }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection