<?php

declare(strict_types=1);

namespace Modules\Invoices\Domain;

use Illuminate\Support\Collection;
use Modules\Invoices\Domain\Enums\StatusEnum;

interface InvoiceRepository
{
    public function addInvoice(
        string $customerName,
        string $customerEmail,
        StatusEnum $status,
        ?Collection $invoiceProductLines = null,
    ): void;
}
