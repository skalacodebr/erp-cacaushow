@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Importar Lista de Contatos</h1>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.importacao-contatos.importar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="nome_lista" class="form-label">Nome da Lista</label>
                    <input type="text" class="form-control @error('nome_lista') is-invalid @enderror" 
                           id="nome_lista" name="nome_lista" value="{{ old('nome_lista') }}" required>
                    @error('nome_lista')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Digite um nome para identificar esta lista de contatos
                    </small>
                </div>
                
                <div class="mb-3">
                    <label for="arquivo" class="form-label">Arquivo Excel</label>
                    <input type="file" class="form-control @error('arquivo') is-invalid @enderror" 
                           id="arquivo" name="arquivo" accept=".xlsx,.xls,.csv" required>
                    @error('arquivo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Formatos aceitos: .xlsx, .xls, .csv
                    </small>
                </div>
                
                <div class="alert alert-info">
                    <h6>Formato esperado do arquivo Excel:</h6>
                    <ul class="mb-0">
                        <li>Coluna A: Nome</li>
                        <li>Coluna B: Email (obrigatório)</li>
                        <li>Coluna C: Telefone (opcional)</li>
                        <li>Coluna D: Empresa (opcional)</li>
                        <li>Coluna E: Cargo (opcional)</li>
                        <li>Coluna F: Observações (opcional)</li>
                    </ul>
                    <p class="mb-0 mt-2">A primeira linha será ignorada (considerada como cabeçalho)</p>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Importar Lista
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