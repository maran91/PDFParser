<?php

namespace App\Providers;

use App\Invoices\Parser\InvoicePdfParser;
use App\Invoices\Parser\PdfParserInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PdfParserInterface::class, InvoicePdfParser::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
