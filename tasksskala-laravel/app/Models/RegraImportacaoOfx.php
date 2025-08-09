<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegraImportacaoOfx extends Model
{
    use HasFactory;

    protected $table = 'regras_importacao_ofx';

    protected $fillable = [
        'nome',
        'descricao',
        'tipo_conta',
        'campo_busca',
        'padrao_busca',
        'categoria_id',
        'fornecedor_id',
        'cliente_id',
        'conta_bancaria_id',
        'centro_custo_id',
        'ativo',
        'prioridade',
        'acoes_adicionais'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'prioridade' => 'integer',
        'acoes_adicionais' => 'array'
    ];

    public function aplicarRegra($transacao)
    {
        // Verifica se a regra se aplica à transação
        $campo = $this->getCampoDaTransacao($transacao);
        
        if (!$campo) {
            return false;
        }

        // Verifica se o padrão corresponde
        if ($this->usarRegex()) {
            if (!preg_match($this->padrao_busca, $campo)) {
                return false;
            }
        } else {
            if (stripos($campo, $this->padrao_busca) === false) {
                return false;
            }
        }

        // A regra se aplica, retorna as ações a serem tomadas
        return [
            'categoria_id' => $this->categoria_id,
            'fornecedor_id' => $this->fornecedor_id,
            'cliente_id' => $this->cliente_id,
            'conta_bancaria_id' => $this->conta_bancaria_id,
            'centro_custo_id' => $this->centro_custo_id,
            'acoes_adicionais' => $this->acoes_adicionais
        ];
    }

    private function getCampoDaTransacao($transacao)
    {
        switch ($this->campo_busca) {
            case 'beneficiario':
                return $transacao->beneficiario;
            case 'descricao':
                return $transacao->descricao;
            case 'numero_documento':
                return $transacao->numero_documento;
            case 'memo':
                return $transacao->descricao . ' ' . $transacao->beneficiario;
            default:
                return null;
        }
    }

    private function usarRegex()
    {
        // Verifica se o padrão é uma regex (começa e termina com /)
        return preg_match('/^\/.*\/$/', $this->padrao_busca);
    }

    public static function aplicarRegrasAutomaticas($transacao)
    {
        $regrasAplicadas = [];
        
        // Busca regras ativas ordenadas por prioridade
        $regras = self::where('ativo', true)
            ->where('tipo_conta', $transacao->tipo_conta)
            ->orderBy('prioridade', 'desc')
            ->get();

        foreach ($regras as $regra) {
            $resultado = $regra->aplicarRegra($transacao);
            if ($resultado) {
                $regrasAplicadas[] = [
                    'regra' => $regra,
                    'acoes' => $resultado
                ];
                
                // Aplica apenas a primeira regra que corresponder
                break;
            }
        }

        return $regrasAplicadas;
    }
}
