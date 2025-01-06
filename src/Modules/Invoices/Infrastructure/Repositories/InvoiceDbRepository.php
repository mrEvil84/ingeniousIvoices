<?php

declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\InvoiceRepository;
use Modules\Invoices\Entities\Invoice;
use Modules\Invoices\Entities\InvoiceProductLine;
use Ramsey\Uuid\Uuid;

class InvoiceDbRepository implements InvoiceRepository
{
    public function addInvoice(
        string $customerName,
        string $customerEmail,
        StatusEnum $status,
        ?Collection $invoiceProductLines = null
    ): void {

        $invoiceId = Uuid::uuid4()->toString();
        $invoice = Invoice::create(
            [
                'id' => $invoiceId,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'status' => $status->value,
            ]
        );
        if ($invoiceProductLines !== null) {
            $productLines = [];
            foreach ($invoiceProductLines as $invoiceProductLine) {
                $productLines[] = InvoiceProductLine::create(
                    [
                        'id' => Uuid::uuid4()->toString(),
                        'invoice_id' => $invoiceId,
                        'name' => $invoiceProductLine['name'],
                        'price' => $invoiceProductLine['price'],
                        'quantity' => $invoiceProductLine['quantity']
                    ]
                );
            }
            $invoice->invoiceProductLines()->saveMany($productLines);
        }
        $invoice->save();
    }

    public function invoiceExists(string $invoiceId): bool
    {
        return Invoice::query()->find($invoiceId) !== null;
    }

    public function getInvoiceById(string $invoiceId): ?Invoice
    {
        return Invoice::query()->find($invoiceId);
    }
}
