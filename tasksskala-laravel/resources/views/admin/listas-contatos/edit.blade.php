@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Editar Lista: {{ $listaContato->nome }}</h1>
        <a href="{{ route('admin.listas-contatos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.listas-contatos.update', $listaContato) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome da Lista *</label>
                    <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                           id="nome" name="nome" value="{{ old('nome', $listaContato->nome) }}" required>
                    @error('nome')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control @error('descricao') is-invalid @enderror" 
                              id="descricao" name="descricao" rows="3">{{ old('descricao', $listaContato->descricao) }}</textarea>
                    @error('descricao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="ativa" {{ old('status', $listaContato->status) == 'ativa' ? 'selected' : '' }}>Ativa</option>
                        <option value="inativa" {{ old('status', $listaContato->status) == 'inativa' ? 'selected' : '' }}>Inativa</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                    <a href="{{ route('admin.listas-contatos.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection