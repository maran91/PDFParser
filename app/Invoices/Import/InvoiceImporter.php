<?php

namespace App\Invoices\Import;

use App\Invoices\Parser;
use App\Invoices\Parser\InvoicePdfParserException;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;

class InvoiceImporter
{
    public function __construct(private Parser\InvoicePdfParser $parser)
    {
    }

    /**
     * @throws InvoicePdfParserException
     */
    public function import(string $filename): Invoice
    {
        $parsedInvoice = $this->parser->parse($filename);

        return DB::transaction(function () use ($parsedInvoice) {
            $existingRecord = Invoice::where('reference', $parsedInvoice['reference'])->first();

            if ($existingRecord) {
                $existingRecord->delete();
            }

            $invoice = Invoice::create(['reference' => $parsedInvoice['reference']]);

            foreach ($parsedInvoice['items'] as $itemData) {
                $invoice->items()->save(new InvoiceItem($itemData));
            }

            return $invoice;
        });

    }
}
