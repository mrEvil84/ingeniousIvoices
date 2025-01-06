<?php

declare(strict_types=1);

namespace Modules\Invoices\ReadModel;

use Modules\Invoices\Model\Invoice;

interface InvoiceReadModelRepository
{
    public function getInvoice(string $invoiceId): Invoice;
}
