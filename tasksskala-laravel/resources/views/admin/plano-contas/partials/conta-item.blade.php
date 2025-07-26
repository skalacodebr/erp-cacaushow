<div class="conta-item conta-nivel-{{ $nivel }} py-2 border-b 
    {{ $conta->natureza == 'receita' ? 'bg-green-50' : '' }}
    {{ $conta->natureza == 'despesa' ? 'bg-red-50' : '' }}
    {{ $conta->natureza == 'resultado' ? 'bg-blue-50' : '' }}">
    
    <div class="flex items-center justify-between">
        <div class="flex items-center flex-1">
            @if($conta->children->count() > 0)
                <span class="toggle-children mr-2" onclick="toggleChildren({{ $conta->id }})">
                    <i id="icon-{{ $conta->id }}" class="fas fa-chevron-right text-gray-500"></i>
                </span>
            @else
                <span class="mr-2 w-4"></span>
            @endif
            
            <span class="font-mono text-sm mr-3 text-gray-600">{{ $conta->codigo }}</span>
            
            <span class="flex-1 {{ $conta->tipo == 'sintetica' ? 'font-semibold' : '' }}">
                {{ $conta->nome }}
            </span>
            
            <span class="text-xs px-2 py-1 rounded mr-2
                {{ $conta->tipo == 'sintetica' ? 'bg-gray-200' : 'bg-yellow-200' }}">
                {{ $conta->tipo == 'sintetica' ? 'S' : 'A' }}
            </span>
            
            @if(!$conta->ativo)
                <span class="text-xs px-2 py-1 bg-gray-300 rounded mr-2">Inativo</span>
            @endif
            
            @if($conta->dre_formula)
                <span class="text-xs text-blue-600 mr-2" title="Fórmula: {{ $conta->dre_formula }}">
                    <i class="fas fa-calculator"></i>
                </span>
            @endif
        </div>
        
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.plano-contas.show', $conta->id) }}" 
               class="text-blue-600 hover:text-blue-900 text-sm">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.plano-contas.edit', $conta->id) }}" 
               class="text-yellow-600 hover:text-yellow-900 text-sm">
                <i class="fas fa-edit"></i>
            </a>
            @if($conta->children->count() == 0 && $conta->categorias->count() == 0)
                <form action="{{ route('admin.plano-contas.destroy', $conta->id) }}" 
                      method="POST" 
                      class="inline"
                      onsubmit="return confirm('Tem certeza que deseja excluir esta conta?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>
    
    @if($conta->descricao)
        <div class="text-sm text-gray-600 mt-1 ml-8">
            {{ $conta->descricao }}
        </div>
    @endif
</div>

@if($conta->children->count() > 0)
    <div id="children-{{ $conta->id }}" class="hidden">
        @foreach($conta->children as $child)
            @include('admin.plano-contas.partials.conta-item', ['conta' => $child, 'nivel' => $nivel + 1])
        @endforeach
    </div>
@endif