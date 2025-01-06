<?php

declare(strict_types=1);

namespace Modules\Invoices\Events;

use Illuminate\Foundation\Events\Dispatchable;

final readonly class InvoiceSending
{
    use Dispatchable;

    public function __construct(
        private string $invoiceId,
        private string $emailTo,
        private string $subject,
        private string $message,
    ) {
    }

    public function getInvoiceId(): string
    {
        return $this->invoiceId;
    }

    public function getEmailTo(): string
    {
        return $this->emailTo;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
