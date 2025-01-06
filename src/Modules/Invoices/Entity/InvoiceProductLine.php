<?php

declare(strict_types=1);

namespace Modules\Invoices\Entity;

final readonly class InvoiceProductLine
{
    public function __construct(
        public string $id,
        public string $invoiceId,
        public string $name,
        public int $price,
        public int $quantity,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getInvoiceId(): string
    {
        return $this->invoiceId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
