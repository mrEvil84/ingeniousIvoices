<?php

declare(strict_types=1);

namespace Modules\Invoices\Application\Command;

use Illuminate\Support\Collection;

final readonly class AddInvoice
{
    public function __construct(
        private string $customerName,
        private string $customerEmail,
        private ?Collection $invoiceProductLines = null,
    ) {
    }

    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    public function getInvoiceProductLines(): ?Collection
    {
        return $this->invoiceProductLines;
    }

    public static function fromArray(array $data): self
    {
        $invoiceProductLines = null;
        if (array_key_exists('invoice_product_lines', $data)) {
            $invoiceProductLines = collect($data['invoice_product_lines']);
        }

        return new self(
            $data['customer_name'],
            $data['customer_email'],
            $invoiceProductLines
        );
    }
}
