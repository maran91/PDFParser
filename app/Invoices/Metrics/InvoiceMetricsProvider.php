<?php

namespace App\Invoices\Metrics;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;

class InvoiceMetricsProvider
{

    public function fetchMetrics(): array
    {
        $invoiceCount = Invoice::count();

        $averageAmountPerInvoice = 0;

        if ($invoiceCount > 0) {
            $averageAmountPerInvoice = round(
                InvoiceItem::groupBy('invoice_id')->selectRaw('count(invoice_id) as invoice_id')
                    ->groupBy('invoice_id')
                    ->pluck('invoice_id')
                    ->avg(),
                2
            );
        }

        $averageProductPrice = round(
            DB::table('invoice_items')->select(DB::raw('AVG(price_total) as average_price'))
                ->groupBy('invoice_id')
                ->pluck('average_price')
                ->avg(),
            2
        );

        $averagePrice = round(
            DB::table('invoice_items')->select(DB::raw('SUM(price_total) as average_price'))
                ->groupBy('invoice_id')
                ->pluck('average_price')
                ->avg(),
            2
        );

        return [
            'invoiceCount' => $invoiceCount,
            'averageAmountPerInvoice' => $averageAmountPerInvoice,
            'averageProductPrice' => $averageProductPrice,
            'averagePrice' => $averagePrice
        ];
    }
}
