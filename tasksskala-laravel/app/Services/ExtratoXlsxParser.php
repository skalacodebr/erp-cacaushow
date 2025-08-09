<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;
use Exception;

class ExtratoXlsxParser
{
    private $arquivo;
    private $colunas = [
        'data' => 'Data',
        'observacao' => 'observacao',
        'data_balancete' => 'Data balancete',
        'agencia_origem' => 'Agencia Origem',
        'lote' => 'Lote',
        'numero_documento' => 'Numero Documento',
        'cod_historico' => 'Cod. Historico',
        'historico' => 'Historico',
        'valor' => 'Valor R$',
        'inf' => 'Inf.',
        'detalhamento' => 'Detalhamento Hist.'
    ];

    public function __construct($arquivo)
    {
        $this->arquivo = $arquivo;
    }

    public function parse()
    {
        try {
            $spreadsheet = IOFactory::load($this->arquivo);
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();
            
            // Encontra a linha com os cabeçalhos
            $headerRow = null;
            $headerIndex = null;
            
            foreach ($data as $index => $row) {
                if ($this->isHeaderRow($row)) {
                    $headerRow = $row;
                    $headerIndex = $index;
                    break;
                }
            }
            
            if (!$headerRow) {
                throw new Exception('Não foi possível encontrar os cabeçalhos do arquivo');
            }
            
            // Mapeia as colunas
            $columnMap = $this->mapColumns($headerRow);
            
            // Processa as transações
            $transactions = [];
            $accountInfo = $this->extractAccountInfo($data);
            
            for ($i = $headerIndex + 1; $i < count($data); $i++) {
                $row = $data[$i];
                
                // Pula linhas vazias ou de saldo
                if (empty($row[$columnMap['data']]) || 
                    stripos($row[$columnMap['historico']] ?? '', 'Saldo') !== false) {
                    continue;
                }
                
                $transaction = $this->parseTransaction($row, $columnMap);
                if ($transaction) {
                    $transactions[] = $transaction;
                }
            }
            
            return [
                'account' => $accountInfo,
                'transactions' => $transactions
            ];
            
        } catch (Exception $e) {
            throw new Exception('Erro ao processar arquivo XLSX: ' . $e->getMessage());
        }
    }

    private function isHeaderRow($row)
    {
        if (!is_array($row)) {
            return false;
        }
        
        $headerKeywords = ['Data', 'Historico', 'Valor', 'Documento'];
        $matchCount = 0;
        
        foreach ($row as $cell) {
            if (!is_string($cell)) continue;
            
            foreach ($headerKeywords as $keyword) {
                if (stripos($cell, $keyword) !== false) {
                    $matchCount++;
                    break;
                }
            }
        }
        
        return $matchCount >= 3;
    }

    private function mapColumns($headerRow)
    {
        $map = [];
        
        foreach ($headerRow as $index => $header) {
            if (!is_string($header)) continue;
            
            $header = trim($header);
            
            // Mapeia colunas baseado em palavras-chave
            if (preg_match('/^Data$/i', $header) || $header === 'Data') {
                $map['data'] = $index;
            } elseif (stripos($header, 'observa') !== false) {
                $map['observacao'] = $index;
            } elseif (stripos($header, 'balancete') !== false) {
                $map['data_balancete'] = $index;
            } elseif (stripos($header, 'Agencia Origem') !== false) {
                $map['agencia_origem'] = $index;
            } elseif (stripos($header, 'Lote') !== false) {
                $map['lote'] = $index;
            } elseif (stripos($header, 'Numero Documento') !== false) {
                $map['numero_documento'] = $index;
            } elseif (preg_match('/Cod.*Historico/i', $header)) {
                $map['cod_historico'] = $index;
            } elseif ($header === 'Historico' || stripos($header, 'Historico') !== false) {
                $map['historico'] = $index;
            } elseif (stripos($header, 'Valor') !== false) {
                $map['valor'] = $index;
            } elseif (stripos($header, 'Inf') !== false) {
                $map['inf'] = $index;
            } elseif (stripos($header, 'Detalhamento') !== false) {
                $map['detalhamento'] = $index;
            }
        }
        
        return $map;
    }

    private function parseTransaction($row, $columnMap)
    {
        // Verifica se tem data e valor
        if (!isset($columnMap['data']) || !isset($columnMap['valor'])) {
            return null;
        }
        
        $dataStr = $row[$columnMap['data']] ?? null;
        $valorStr = $row[$columnMap['valor']] ?? null;
        
        if (!$dataStr || !$valorStr) {
            return null;
        }
        
        // Parse da data
        $data = $this->parseDate($dataStr);
        
        // Parse do valor
        $valor = $this->parseAmount($valorStr);
        
        // Determina se é crédito ou débito
        $tipo = 'CREDIT'; // Padrão
        $inf = $row[$columnMap['inf']] ?? '';
        
        if (trim($inf) === 'D') {
            $tipo = 'DEBIT';
            $valor = -abs($valor); // Garante que débito seja negativo
        } elseif (trim($inf) === 'C') {
            $tipo = 'CREDIT';
            $valor = abs($valor); // Garante que crédito seja positivo
        }
        
        // Extrai descrição e beneficiário
        $historico = $row[$columnMap['historico']] ?? '';
        $detalhamento = $row[$columnMap['detalhamento']] ?? '';
        
        $descricao = trim($historico);
        if ($detalhamento) {
            $descricao .= ' - ' . trim($detalhamento);
        }
        
        // Identifica o beneficiário a partir do histórico
        $beneficiario = $this->extractBeneficiario($historico, $detalhamento);
        
        return [
            'type' => $tipo,
            'date' => $data,
            'amount' => $valor,
            'fitid' => $this->generateFitId($row, $columnMap),
            'checknum' => null,
            'refnum' => $row[$columnMap['numero_documento']] ?? null,
            'memo' => $descricao,
            'name' => $beneficiario,
            'payee' => $beneficiario,
            'extra_info' => [
                'cod_historico' => $row[$columnMap['cod_historico']] ?? null,
                'agencia_origem' => $row[$columnMap['agencia_origem']] ?? null,
                'lote' => $row[$columnMap['lote']] ?? null,
                'detalhamento' => $detalhamento
            ]
        ];
    }

    private function parseDate($dateValue)
    {
        // Se for um número serial do Excel
        if (is_numeric($dateValue)) {
            return Carbon::instance(Date::excelToDateTimeObject($dateValue));
        }
        
        // Tenta diversos formatos de data brasileiros
        $formats = [
            'd/m/Y',
            'd/m/y',
            'd-m-Y',
            'd-m-y',
            'd.m.Y',
            'd.m.y',
            'Y-m-d'
        ];
        
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $dateValue);
            } catch (Exception $e) {
                continue;
            }
        }
        
        // Última tentativa com Carbon parse
        try {
            return Carbon::parse($dateValue);
        } catch (Exception $e) {
            throw new Exception("Não foi possível processar a data: {$dateValue}");
        }
    }

    private function parseAmount($amount)
    {
        if (!$amount) {
            return 0;
        }
        
        // Remove espaços
        $amount = trim($amount);
        $amount = str_replace(' ', '', $amount);
        $amount = str_replace('R$', '', $amount);
        
        // Trata formato brasileiro (1.234,56)
        if (preg_match('/^\d{1,3}(\.\d{3})*,\d{2}$/', $amount)) {
            $amount = str_replace('.', '', $amount);
            $amount = str_replace(',', '.', $amount);
        } else {
            // Formato internacional (1,234.56)
            $amount = str_replace(',', '', $amount);
        }
        
        return (float) $amount;
    }

    private function extractBeneficiario($historico, $detalhamento)
    {
        // Padrões comuns em extratos bancários brasileiros
        $patterns = [
            '/PIX\s+(?:ENVIADO|RECEBIDO)\s+(.+)/i',
            '/TED\s+(?:ENVIADO|RECEBIDO)\s+(.+)/i',
            '/DOC\s+(?:ENVIADO|RECEBIDO)\s+(.+)/i',
            '/TRANSF\s+(?:DE|PARA)\s+(.+)/i',
            '/PAG\s+(.+)/i',
            '/COMPRA\s+(.+)/i',
            '/Cielo\s+(.+)/i',
            '/REDE\s+(.+)/i',
            '/STONE\s+(.+)/i',
            '/PAGSEGURO\s+(.+)/i',
            '/MERCADO\s*PAGO\s+(.+)/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $historico, $matches)) {
                return trim($matches[1]);
            }
        }
        
        // Se não encontrar padrão, usa o detalhamento ou o próprio histórico
        if ($detalhamento) {
            return trim($detalhamento);
        }
        
        return trim($historico);
    }

    private function generateFitId($row, $columnMap)
    {
        // Gera um ID único baseado nos dados da transação
        $data = $row[$columnMap['data']] ?? '';
        $valor = $row[$columnMap['valor']] ?? '';
        $documento = $row[$columnMap['numero_documento']] ?? '';
        $historico = $row[$columnMap['historico']] ?? '';
        
        $string = $data . $valor . $documento . $historico;
        return 'XLSX_' . md5($string) . '_' . time();
    }

    private function extractAccountInfo($data)
    {
        $accountInfo = [
            'bankid' => null,
            'accountid' => null,
            'accounttype' => 'CHECKING',
            'balance' => null,
            'balance_date' => null,
            'branch' => null
        ];
        
        // Procura informações da conta nas primeiras linhas
        foreach ($data as $row) {
            if (!is_array($row)) continue;
            
            $rowText = implode(' ', array_map('strval', $row));
            
            // Procura agência
            if (preg_match('/Agencia\s+([0-9X]+)/i', $rowText, $matches)) {
                $accountInfo['branch'] = $matches[1];
            }
            
            // Procura conta corrente
            if (preg_match('/Conta\s+corrente\s+(\d+)/i', $rowText, $matches)) {
                $accountInfo['accountid'] = $matches[1];
            }
            
            // Se encontrou as informações básicas, para
            if ($accountInfo['branch'] && $accountInfo['accountid']) {
                break;
            }
        }
        
        return $accountInfo;
    }
}