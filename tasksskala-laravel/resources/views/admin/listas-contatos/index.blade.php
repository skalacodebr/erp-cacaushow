@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Listas de Contatos</h1>
        <div>
            <a href="{{ route('admin.importacao-contatos.index') }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Importar Lista
            </a>
            <a href="{{ route('admin.listas-contatos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nova Lista
            </a>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
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
                            <th>Total de Contatos</th>
                            <th>Status</th>
                            <th>Criada em</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($listas as $lista)
                            <tr>
                                <td>{{ $lista->nome }}</td>
                                <td>{{ $lista->contatos_count }}</td>
                                <td>
                                    <span class="badge bg-{{ $lista->status == 'ativa' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($lista->status) }}
                                    </span>
                                </td>
                                <td>{{ $lista->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.listas-contatos.show', $lista) }}" 
                                           class="btn btn-sm btn-info" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.listas-contatos.edit', $lista) }}" 
                                           class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.listas-contatos.destroy', $lista) }}" 
                                              method="POST" style="display: inline-block;"
                                              onsubmit="return confirm('Tem certeza que deseja excluir esta lista?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Nenhuma lista encontrada</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $listas->links() }}
        </div>
    </div>
</div>
@endsection