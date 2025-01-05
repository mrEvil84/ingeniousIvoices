<?php

declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Repositories;

use Modules\Invoices\Entities\Invoice;
use Modules\Invoices\ReadModel\InvoiceReadModelRepository;

final readonly class InvoiceReadModelDbRepository implements InvoiceReadModelRepository
{
    public function getInvoice(int $invoiceId): Invoice
    {
        return Invoice::query()->findOrFail($invoiceId);
    }
}
