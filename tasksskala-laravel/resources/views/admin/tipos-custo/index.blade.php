@extends('layouts.admin')

@section('title', 'Tipos de Custo')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Tipos de Custo</h1>
        <a href="{{ route('admin.tipos-custo.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Novo Tipo de Custo
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded">
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Nome
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Descrição
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Ordem
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @forelse($tiposCusto as $tipo)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                            <div class="text-sm font-medium text-gray-900">{{ $tipo->nome }}</div>
                            <div class="text-sm text-gray-500">Slug: {{ $tipo->slug }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                            <div class="text-sm text-gray-900">{{ $tipo->descricao ?? 'Sem descrição' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                            <div class="text-sm text-gray-900">{{ $tipo->ordem }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $tipo->ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $tipo->ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200 text-sm font-medium">
                            <a href="{{ route('admin.tipos-custo.show', $tipo) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Ver</a>
                            <a href="{{ route('admin.tipos-custo.edit', $tipo) }}" class="text-yellow-600 hover:text-yellow-900 mr-2">Editar</a>
                            <form action="{{ route('admin.tipos-custo.destroy', $tipo) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Tem certeza?')">
                                    Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Nenhum tipo de custo cadastrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $tiposCusto->links() }}
    </div>
</div>
@endsection