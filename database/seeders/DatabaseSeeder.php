<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Invoice::factory(0)->create()
          ->each(function ($invoice) {
                InvoiceItem::factory(0)->create(
                        [
                            'invoice_id' => $invoice->id,
                            'barcode' => fake()->randomDigit(),
                            'price' => fake()->randomDigit(),
                            'price_total' => fake()->randomDigit(),
                            'quantity' => fake()->randomDigit()
                        ]);
            });
    }
}
