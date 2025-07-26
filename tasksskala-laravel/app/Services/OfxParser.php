<?php

namespace App\Services;

use DateTime;
use DOMDocument;
use Exception;

class OfxParser
{
    private $ofxContent;
    private $xmlContent;

    public function __construct($ofxContent)
    {
        $this->ofxContent = $ofxContent;
        $this->convertToXml();
    }

    private function convertToXml()
    {
        // Remove headers and get only the OFX content
        $ofxContent = $this->ofxContent;
        
        // Find where the actual OFX data starts
        $ofxStart = strpos($ofxContent, '<OFX>');
        if ($ofxStart !== false) {
            $ofxContent = substr($ofxContent, $ofxStart);
        }
        
        // Replace OFX tags with proper XML
        $xmlContent = $ofxContent;
        
        // Close self-closing tags
        $xmlContent = preg_replace('/<([A-Z0-9_]+)>([^<]+)$/im', '<\1>\2</\1>', $xmlContent);
        $xmlContent = preg_replace('/<([A-Z0-9_]+)>([^<]+)\n/im', '<\1>\2</\1>\n', $xmlContent);
        
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
            throw new Exception('Failed to parse OFX file');
        }
        
        $transactions = [];
        
        // Get bank transactions
        $stmttrns = $dom->getElementsByTagName('STMTTRN');
        
        foreach ($stmttrns as $transaction) {
            $trans = [
                'type' => $this->getNodeValue($transaction, 'TRNTYPE'),
                'date' => $this->parseDate($this->getNodeValue($transaction, 'DTPOSTED')),
                'amount' => $this->parseAmount($this->getNodeValue($transaction, 'TRNAMT')),
                'fitid' => $this->getNodeValue($transaction, 'FITID'),
                'checknum' => $this->getNodeValue($transaction, 'CHECKNUM'),
                'refnum' => $this->getNodeValue($transaction, 'REFNUM'),
                'memo' => $this->getNodeValue($transaction, 'MEMO'),
                'name' => $this->getNodeValue($transaction, 'NAME'),
                'payee' => $this->getNodeValue($transaction, 'PAYEE'),
            ];
            
            $transactions[] = $trans;
        }
        
        // Get account info
        $accountInfo = [
            'bankid' => $this->getNodeValue($dom, 'BANKID'),
            'accountid' => $this->getNodeValue($dom, 'ACCTID'),
            'accounttype' => $this->getNodeValue($dom, 'ACCTTYPE'),
            'balance' => $this->parseAmount($this->getNodeValue($dom, 'BALAMT')),
            'balance_date' => $this->parseDate($this->getNodeValue($dom, 'DTASOF')),
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
        
        return (float) str_replace(',', '.', $amount);
    }
}