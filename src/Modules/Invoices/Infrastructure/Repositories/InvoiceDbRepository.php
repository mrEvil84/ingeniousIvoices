<?php

declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\InvoiceRepository;
use Modules\Invoices\Entity\Invoice as InvoiceEntity;
use Modules\Invoices\Entity\InvoiceProductLine as InvoiceProductLineEntity;
use Modules\Invoices\Model\Invoice;
use Modules\Invoices\Model\InvoiceProductLine;
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

    public function getInvoiceEntityById(string $invoiceId): ?InvoiceEntity
    {
        $invoice = Invoice::query()->find($invoiceId);
        $productLines = new Collection();

        foreach ($invoice->invoiceProductLines()->get() as $invoiceProductLine) {
            $productLines->add(
                new InvoiceProductLineEntity(
                    $invoiceProductLine->id,
                    $invoiceProductLine->invoice_id,
                    $invoiceProductLine->name,
                    $invoiceProductLine->price,
                    $invoiceProductLine->quantity,
                )
            );
        }

        if ($invoice !== null) {
            return new InvoiceEntity(
                $invoice->id,
                $invoice->customer_name,
                $invoice->customer_email,
                StatusEnum::from($invoice->status),
                $productLines
            );
        }
        return null;
    }


    public function setInvoiceStatus(string $invoiceId, StatusEnum $status): void
    {
        $invoice = Invoice::query()->findOrFail($invoiceId);
        $invoice->status = $status->value;
        $invoice->save();
    }
}
