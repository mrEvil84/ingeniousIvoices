<?php

declare(strict_types=1);

namespace Modules\Invoices\ReadModel\Dtos;

use Illuminate\Support\Collection;
use Modules\Invoices\Domain\Enums\StatusEnum;

final readonly class InvoiceDto
{
    public function __construct(
        private string $id,
        private string $customerName,
        private string $customerEmail,
        private StatusEnum $status,
        private ?Collection $invoiceProductLines = null,
        private ?int $totalPrice = 0,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail,
            'status' => $this->status->value,
            'invoice_product_lines' => $this->invoiceProductLines->toArray(),
            'total_price' => $this->totalPrice,
        ];
    }
}
