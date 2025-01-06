<?php

declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Repositories;

use Modules\Invoices\Model\Invoice;
use Modules\Invoices\ReadModel\InvoiceReadModelRepository;

final readonly class InvoiceReadModelDbRepository implements InvoiceReadModelRepository
{
    public function getInvoice(string $invoiceId): Invoice
    {
        return Invoice::query()->findOrFail($invoiceId);
    }
}
