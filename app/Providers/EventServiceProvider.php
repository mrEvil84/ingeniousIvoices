<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Invoices\Events\InvoiceSending;
use Modules\Invoices\Listeners\InvoiceDelivered;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;
use Modules\Notifications\Listeners\SendInvoice as NotificationSendInvoiceListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        InvoiceSending::class => [
            NotificationSendInvoiceListener::class,
        ],
        ResourceDeliveredEvent::class => [
            InvoiceDelivered::class,
        ]
    ];

    public function boot()
    {
        parent::boot();
    }
}
