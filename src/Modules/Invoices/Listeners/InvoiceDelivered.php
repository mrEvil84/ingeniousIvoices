<?php

declare(strict_types=1);

namespace Modules\Invoices\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Invoices\Application\InvoiceService;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;

final readonly class InvoiceDelivered
{
    public function __construct(
        private InvoiceService $invoiceService,
    ) {
    }

    public function handle(ResourceDeliveredEvent $event): void
    {
        try {
            $this->invoiceService->setStatusSentToClient($event->resourceId->toString());
            Log::channel('sending_email_log')->info(
               sprintf(
                   'Invoice %s hanged status %s', $event->resourceId->toString(), StatusEnum::SentToClient->value
               ),
            );
        } catch (\Throwable $exception) {
            Log::channel('sending_email_log')->info(
                $exception->getMessage(),
            );
        }
    }
}
