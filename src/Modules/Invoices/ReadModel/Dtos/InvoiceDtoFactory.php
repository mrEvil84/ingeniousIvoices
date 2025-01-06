<?php

declare(strict_types=1);

namespace Modules\Invoices\ReadModel\Dtos;

use Illuminate\Support\Collection;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Model\Invoice;
use Modules\Invoices\Model\InvoiceProductLine;

final readonly class InvoiceDtoFactory
{
    public function createInvoiceDtoFromEntity(Invoice $invoice): InvoiceDto
    {
        $collection = new Collection();
        $totalPrice = 0;
        $productLines = $invoice->invoiceProductLines()->get();

        /** @var InvoiceProductLine $productLine */
        foreach ($productLines as $productLine) {
            $unitPrice = $productLine->price * $productLine->quantity;
            $totalPrice += $unitPrice;

            $collection->add(new InvoiceProductLineDto(
                $productLine->id,
                $productLine->invoice_id,
                $productLine->name,
                $productLine->price,
                $productLine->quantity,
                $unitPrice
            ));
        }

        return new InvoiceDto(
            $invoice->id,
            $invoice->customer_name,
            $invoice->customer_email,
            StatusEnum::from($invoice->status),
            $collection,
            $totalPrice
        );
    }
}
