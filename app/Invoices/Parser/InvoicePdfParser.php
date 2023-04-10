<?php

namespace App\Invoices\Parser;

use Exception;
use Smalot\PdfParser\Parser;

class InvoicePdfParser implements PdfParserInterface
{
    public function __construct(private Parser $parser)
    {
    }


    /**
     * @throws InvoicePdfParserException
     * @throws Exception
     */
    public function parse($failname):
    array {
        $content = $this
            ->parser
            ->parseFile($failname)->getText();
            $data = explode('value1', $content);

        if (count($data) < 2) {
            throw new InvoicePdfParserException('PDF file is malformed, unable to find invoice information');
        }

        $reference = $this->parseReference($data[0]);
        $items = $this->parseItems($data[1]);
        return [
            'reference' => $reference,
            'items' => $items
        ];
    }

    /**
     * @throws Exception
     */
    private function parseReference($invoiceInfo): string
    {
        // getting everything between "No. " and " ORIGINAL"
        if (!preg_match('/No\.\s*(.+?)\s*ORIGINAL/', $invoiceInfo, $matches)) {
            throw new InvoicePdfParserException('Unable to find invoice reference number');
        }

        return $matches[1];
    }

    /**
     * @throws InvoicePdfParserException
     */
    private function parseItems($invoiceInfo): array
    {
        $data = explode('0%0,00', $invoiceInfo);
        $pdfItems = [];

        if (!$data) {
            throw new InvoicePdfParserException('PDF file is malformed, unable to find invoice information');
        }

        foreach ($data as $item) {
            $arrayEntry = [];

            // getting 10 numbers in a row, space after the 10 numers and 3 numbers after space to get barcode
            if (preg_match('/(\d{10}\s\d{3})/', $item, $matches)) {
                $arrayEntry['barcode'] = (int)preg_replace('/\s+/', '', $matches[0]);
            } else {
                continue;
            }

            // cutting string to half from barcode
            $item = preg_split('/(\d{10}\s\d{3})/', $item);
            $item = $item[1];

            // getting all numbers before "," and 2 numbers after to get unit price
            preg_match('/(\d*)\,(\d{1,2})/', $item, $matches);
            $arrayEntry['price'] = str_replace(',', '.', $matches[0]);
            $priceAsString = $matches[0];
            $item = explode('sz', $item);
            $arrayEntry['price_total'] = trim(str_replace(',', '.', str_replace('t' . $priceAsString, '', $item[1])));
            $arrayEntry['quantity'] = bcdiv($arrayEntry['price_total'], $arrayEntry['price']);
            $pdfItems[] = $arrayEntry;
        }
        return $pdfItems;
    }
}

