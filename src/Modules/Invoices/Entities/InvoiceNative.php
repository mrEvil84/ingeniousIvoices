<?php

declare(strict_types=1);

namespace Modules\Invoices\Entities;

use DateTimeInterface;
use Modules\Invoices\Domain\Enums\StatusEnum;

final readonly class InvoiceNative
{
    public function __construct(
        private int $id,
        private string $customerName,
        private string $customerEmail,
        private StatusEnum $status,
        private DateTimeInterface $createdAt,
        private DateTimeInterface $updatedAt,
        private array $productLines = [],
    ) {
    }

    public function getId(): int
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

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @return InvoiceProductLine[]
     */
    public function getProductLines(): array
    {
        return $this->productLines;
    }
}
