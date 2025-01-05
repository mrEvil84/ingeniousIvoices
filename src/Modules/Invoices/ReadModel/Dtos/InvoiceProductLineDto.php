<?php

declare(strict_types=1);

namespace Modules\Invoices\ReadModel\Dtos;

use Illuminate\Contracts\Support\Arrayable;

final readonly class InvoiceProductLineDto implements Arrayable
{
    public function __construct(
        private int $id,
        private string $invoiceId,
        private string $name,
        private int $price,
        private int $quantity,
        private int $totalUnitPrice,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'invoice_id' => $this->invoiceId,
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'total_price' => $this->totalUnitPrice,
        ];
    }
}
