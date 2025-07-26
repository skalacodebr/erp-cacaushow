@extends('layouts.admin')

@section('title', 'Plano de Contas')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Plano de Contas</h1>
        <a href="{{ route('admin.plano-contas.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-plus mr-2"></i>Nova Conta
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

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="p-6">
            <div class="text-sm text-gray-600 mb-4">
                <span class="inline-block w-4 h-4 bg-green-100 mr-1"></span> Receitas
                <span class="inline-block w-4 h-4 bg-red-100 ml-4 mr-1"></span> Despesas
                <span class="inline-block w-4 h-4 bg-blue-100 ml-4 mr-1"></span> Resultado
                <span class="ml-4">|</span>
                <span class="font-bold ml-4">S</span> = Sintética (agrupadora)
                <span class="font-bold ml-4">A</span> = Analítica (lançamentos)
            </div>
            
            @foreach($planoContas as $conta)
                @include('admin.plano-contas.partials.conta-item', ['conta' => $conta, 'nivel' => 0])
            @endforeach
        </div>
    </div>
</div>

<style>
    .conta-nivel-0 { padding-left: 0; }
    .conta-nivel-1 { padding-left: 2rem; }
    .conta-nivel-2 { padding-left: 4rem; }
    .conta-nivel-3 { padding-left: 6rem; }
    .conta-nivel-4 { padding-left: 8rem; }
    
    .conta-item {
        transition: all 0.2s ease;
    }
    
    .conta-item:hover {
        background-color: #f7fafc;
    }
    
    .toggle-children {
        cursor: pointer;
        user-select: none;
    }
</style>

<script>
function toggleChildren(id) {
    const childrenDiv = document.getElementById('children-' + id);
    const icon = document.getElementById('icon-' + id);
    
    if (childrenDiv.classList.contains('hidden')) {
        childrenDiv.classList.remove('hidden');
        icon.classList.remove('fa-chevron-right');
        icon.classList.add('fa-chevron-down');
    } else {
        childrenDiv.classList.add('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-right');
    }
}
</script>
@endsection