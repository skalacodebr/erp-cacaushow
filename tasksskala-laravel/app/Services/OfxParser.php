<?php

namespace App\Services;

use DateTime;
use DOMDocument;
use Exception;

class OfxParser
{
    private $ofxContent;
    private $xmlContent;
    private $encoding;

    public function __construct($ofxContent)
    {
        $this->ofxContent = $ofxContent;
        $this->detectEncoding();
        $this->convertToXml();
    }

    private function detectEncoding()
    {
        // Detecta encoding do arquivo (ISO-8859-1 é comum em bancos brasileiros)
        if (preg_match('/CHARSET[:\s]+([^\s\r\n]+)/i', $this->ofxContent, $matches)) {
            $charset = strtoupper(trim($matches[1]));
            if ($charset === 'ISO-8859-1' || $charset === 'WINDOWS-1252') {
                $this->encoding = 'ISO-8859-1';
                $this->ofxContent = mb_convert_encoding($this->ofxContent, 'UTF-8', 'ISO-8859-1');
            } else {
                $this->encoding = 'UTF-8';
            }
        } else {
            // Tenta detectar automaticamente
            $this->encoding = mb_detect_encoding($this->ofxContent, ['UTF-8', 'ISO-8859-1', 'WINDOWS-1252'], true);
            if ($this->encoding !== 'UTF-8') {
                $this->ofxContent = mb_convert_encoding($this->ofxContent, 'UTF-8', $this->encoding);
            }
        }
    }

    private function convertToXml()
    {
        // Remove headers and get only the OFX content
        $ofxContent = $this->ofxContent;
        
        // Remove BOM se existir
        $ofxContent = preg_replace('/^\xEF\xBB\xBF/', '', $ofxContent);
        
        // Find where the actual OFX data starts
        $ofxStart = strpos($ofxContent, '<OFX>');
        if ($ofxStart !== false) {
            $ofxContent = substr($ofxContent, $ofxStart);
        }
        
        // Limpa caracteres especiais que podem causar problemas
        $ofxContent = str_replace('&', '&amp;', $ofxContent);
        
        // Replace OFX tags with proper XML
        $xmlContent = $ofxContent;
        
        // Close self-closing tags (melhorado para tags brasileiras)
        $xmlContent = preg_replace('/<([A-Z0-9_]+)>([^<]+)$/im', '<\1>\2</\1>', $xmlContent);
        $xmlContent = preg_replace('/<([A-Z0-9_]+)>([^<]+)\n/im', '<\1>\2</\1>\n', $xmlContent);
        
        // Corrige tags específicas de bancos brasileiros
        $xmlContent = preg_replace('/<([A-Z0-9_]+)>([^<]*?)(?=<[A-Z0-9_]+>)/i', '<\1>\2</\1>', $xmlContent);
        
        // Add XML declaration
        $xmlContent = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $xmlContent;
        
        $this->xmlContent = $xmlContent;
    }

    public function parse()
    {
        $dom = new DOMDocument();
        
        // Suppress warnings for malformed XML
        libxml_use_internal_errors(true);
        
        if (!$dom->loadXML($this->xmlContent)) {
            throw new Exception('Falha ao processar arquivo OFX. Verifique se o arquivo está no formato correto.');
        }
        
        $transactions = [];
        
        // Get bank transactions
        $stmttrns = $dom->getElementsByTagName('STMTTRN');
        
        foreach ($stmttrns as $transaction) {
            // Captura dados da transação com suporte a variações brasileiras
            $trans = [
                'type' => $this->getNodeValue($transaction, 'TRNTYPE'),
                'date' => $this->parseDate($this->getNodeValue($transaction, 'DTPOSTED')),
                'amount' => $this->parseAmount($this->getNodeValue($transaction, 'TRNAMT')),
                'fitid' => $this->getNodeValue($transaction, 'FITID'),
                'checknum' => $this->getNodeValue($transaction, 'CHECKNUM'),
                'refnum' => $this->getNodeValue($transaction, 'REFNUM'),
                'memo' => $this->cleanMemo($this->getNodeValue($transaction, 'MEMO')),
                'name' => $this->getNodeValue($transaction, 'NAME'),
                'payee' => $this->getNodeValue($transaction, 'PAYEE'),
            ];
            
            // Tratamento especial para campos de bancos brasileiros
            if (empty($trans['memo']) && !empty($trans['name'])) {
                $trans['memo'] = $trans['name'];
            }
            
            // Extrai informações adicionais do memo (comum em bancos BR)
            $trans['extra_info'] = $this->extractBrazilianInfo($trans['memo']);
            
            $transactions[] = $trans;
        }
        
        // Get account info
        $accountInfo = [
            'bankid' => $this->getNodeValue($dom, 'BANKID') ?: $this->getNodeValue($dom, 'BANKACCTFROM.BANKID'),
            'accountid' => $this->getNodeValue($dom, 'ACCTID') ?: $this->getNodeValue($dom, 'BANKACCTFROM.ACCTID'),
            'accounttype' => $this->getNodeValue($dom, 'ACCTTYPE') ?: $this->getNodeValue($dom, 'BANKACCTFROM.ACCTTYPE'),
            'balance' => $this->parseAmount($this->getNodeValue($dom, 'BALAMT')),
            'balance_date' => $this->parseDate($this->getNodeValue($dom, 'DTASOF')),
            'branch' => $this->getNodeValue($dom, 'BRANCHID'), // Agência
        ];
        
        return [
            'account' => $accountInfo,
            'transactions' => $transactions
        ];
    }

    private function getNodeValue($node, $tagName)
    {
        if ($node instanceof DOMDocument) {
            $elements = $node->getElementsByTagName($tagName);
            if ($elements->length > 0) {
                return trim($elements->item(0)->nodeValue);
            }
        } else {
            $elements = $node->getElementsByTagName($tagName);
            if ($elements->length > 0) {
                return trim($elements->item(0)->nodeValue);
            }
        }
        
        return null;
    }

    private function parseDate($dateString)
    {
        if (!$dateString) {
            return null;
        }
        
        // OFX date format: YYYYMMDDHHMMSS[.XXX][Z|[+|-]TZ]
        $year = substr($dateString, 0, 4);
        $month = substr($dateString, 4, 2);
        $day = substr($dateString, 6, 2);
        
        $hour = '00';
        $minute = '00';
        $second = '00';
        
        if (strlen($dateString) >= 14) {
            $hour = substr($dateString, 8, 2);
            $minute = substr($dateString, 10, 2);
            $second = substr($dateString, 12, 2);
        }
        
        return new DateTime("$year-$month-$day $hour:$minute:$second");
    }

    private function parseAmount($amount)
    {
        if (!$amount) {
            return 0;
        }
        
        // Remove espaços e caracteres de formatação
        $amount = trim($amount);
        $amount = str_replace(' ', '', $amount);
        
        // Trata formatos brasileiros (1.234,56) e internacionais (1,234.56)
        if (preg_match('/^\d{1,3}(\.\d{3})*,\d{2}$/', $amount)) {
            // Formato brasileiro: 1.234,56
            $amount = str_replace('.', '', $amount);
            $amount = str_replace(',', '.', $amount);
        } else {
            // Formato internacional: 1,234.56
            $amount = str_replace(',', '', $amount);
        }
        
        return (float) $amount;
    }
    
    private function cleanMemo($memo)
    {
        if (!$memo) {
            return '';
        }
        
        // Remove espaços extras e caracteres especiais
        $memo = trim($memo);
        $memo = preg_replace('/\s+/', ' ', $memo);
        
        return $memo;
    }
    
    private function extractBrazilianInfo($memo)
    {
        $info = [];
        
        if (!$memo) {
            return $info;
        }
        
        // Extrai CPF/CNPJ
        if (preg_match('/\d{3}\.\d{3}\.\d{3}-\d{2}/', $memo, $matches)) {
            $info['cpf'] = $matches[0];
        } elseif (preg_match('/\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}/', $memo, $matches)) {
            $info['cnpj'] = $matches[0];
        }
        
        // Extrai número de documento/boleto
        if (preg_match('/DOC[\s:]+(\d+)/i', $memo, $matches)) {
            $info['doc'] = $matches[1];
        }
        
        // Extrai TED/TEF
        if (preg_match('/TED[\s:]+(\d+)/i', $memo, $matches)) {
            $info['ted'] = $matches[1];
        }
        
        // Extrai PIX
        if (preg_match('/PIX/i', $memo)) {
            $info['tipo_transacao'] = 'PIX';
            
            // Tenta extrair chave PIX
            if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $memo, $matches)) {
                $info['chave_pix'] = $matches[0];
            } elseif (preg_match('/\(\d{2}\)\s?\d{4,5}-?\d{4}/', $memo, $matches)) {
                $info['chave_pix'] = $matches[0];
            }
        }
        
        // Extrai referência de cartão
        if (preg_match('/CARTAO[\s:]+(\d{4})/i', $memo, $matches)) {
            $info['cartao'] = $matches[1];
        }
        
        // Identifica tipo de operação
        if (preg_match('/SAQUE|DEPOSITO|TRANSFERENCIA|TED|DOC|PIX|TARIFA|IOF|COMPRA|PAGAMENTO/i', $memo, $matches)) {
            $info['operacao'] = strtoupper($matches[0]);
        }
        
        return $info;
    }
}