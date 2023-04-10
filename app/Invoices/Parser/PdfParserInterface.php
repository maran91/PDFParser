<?php

namespace App\Invoices\Parser;

interface PdfParserInterface
{
    public function parse(string $failname): array;
}
