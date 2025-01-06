<?php

declare(strict_types=1);

namespace Modules\Notifications\Listeners;

use Modules\Invoices\Events\InvoiceSending;
use Modules\Notifications\Api\Dtos\NotifyData;
use Modules\Notifications\Api\NotificationFacadeInterface;
use Ramsey\Uuid\Uuid;

final readonly class SendInvoice
{
    public function __construct(
        private NotificationFacadeInterface $notificationFacade,
    ) {
    }

    public function handle(InvoiceSending $event): void
    {
        $invoiceId = $event->getInvoiceId();
        $this->notificationFacade->notify(
            new NotifyData(
                Uuid::fromString($invoiceId),
                $event->getEmailTo(),
                $event->getSubject(),
                $event->getMessage(),
            ),
        );
    }
}
