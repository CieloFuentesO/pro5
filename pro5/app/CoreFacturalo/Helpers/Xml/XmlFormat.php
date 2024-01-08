<?php
namespace App\CoreFacturalo\Helpers\Xml;

use Illuminate\Support\Facades\Log;

class XmlFormat
{
    public static function format($xml, $formatOutput = TRUE, $declaration = TRUE)
    {
        Log::info($xml);
        $sxe = ($xml instanceof \SimpleXMLElement) ? $xml : simplexml_load_string($xml);
        $domElement = dom_import_simplexml($sxe);
        $domDocument = $domElement->ownerDocument;
        $domDocument->preserveWhiteSpace = false;
        $domDocument->formatOutput = (bool)$formatOutput;
        $domDocument->loadXML($sxe->asXML(), LIBXML_NOBLANKS);

        return (bool)$declaration ? $domDocument->saveXML() : $domDocument->saveXML($domDocument->documentElement);
    }
}