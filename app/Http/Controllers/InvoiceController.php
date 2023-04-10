<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadPdfRequest;
use App\Invoices\Import\InvoiceImporter;
use App\Invoices\Metrics\InvoiceMetricsProvider;
use App\Invoices\Parser\InvoicePdfParserException;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceMetricsProvider $invoiceMetricsProvider,
        private InvoiceImporter $invoiceImporter
    ) {
    }

    public function show()
    {
        return view('invoice.index', $this->invoiceMetricsProvider->fetchMetrics());
    }

    /**
     * @throws Exception
     */
    public function store(UploadPdfRequest $request): RedirectResponse
    {
        try {
            $this->invoiceImporter->import($request->file('PDF'));
            return redirect()->back()
                ->with('success', 'Data stored successfully!');
        } catch (InvoicePdfParserException $e) {
            //DB::rollback();
            Log::error($e);
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

