<?php

declare(strict_types=1);

namespace Modules\Invoices\Entity;


use Illuminate\Support\Collection;
use Modules\Invoices\Domain\Enums\StatusEnum;

final readonly class Invoice
{
    public function __construct(
        private string $id,
        private string $customerName,
        private string $customerEmail,
        private StatusEnum $status,
        private ?Collection $invoiceProductLines = null,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    public function getStatus(): StatusEnum
    {
        return $this->status;
    }

    public function getInvoiceProductLines(): ?Collection
    {
        return $this->invoiceProductLines;
    }
}
