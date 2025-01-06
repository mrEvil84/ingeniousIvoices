<?php

declare(strict_types=1);

namespace Modules\Invoices\Domain;

use Illuminate\Support\Collection;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Entity\Invoice as InvoiceEntity;

interface InvoiceRepository
{
    public function addInvoice(
        string $customerName,
        string $customerEmail,
        StatusEnum $status,
        ?Collection $invoiceProductLines = null,
    ): void;

    public function invoiceExists(string $invoiceId): bool;

    public function getInvoiceEntityById(string $invoiceId): ?InvoiceEntity;

    public function setInvoiceStatus(string $invoiceId, StatusEnum $status): void;
}
