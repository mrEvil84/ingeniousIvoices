<?php

declare(strict_types=1);

namespace Modules\Invoices\ReadModel;

use Modules\Invoices\ReadModel\Dtos\InvoiceDto;
use Modules\Invoices\ReadModel\Dtos\InvoiceDtoFactory;

final readonly class InvoiceReadModel
{
    public function __construct(
        private InvoiceReadModelRepository $repository,
        private InvoiceDtoFactory $invoiceDtoFactory,
    ) {
    }

    public function getInvoice(string $invoiceId): InvoiceDto
    {
        return $this->invoiceDtoFactory->createInvoiceDtoFromEntity(
            $this->repository->getInvoice($invoiceId)
        );
    }
}
