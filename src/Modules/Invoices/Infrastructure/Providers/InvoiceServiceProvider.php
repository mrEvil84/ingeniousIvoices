<?php

declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Invoices\Domain\InvoiceRepository;
use Modules\Invoices\Infrastructure\Repositories\InvoiceDbRepository;
use Modules\Invoices\Infrastructure\Repositories\InvoiceReadModelDbRepository;
use Modules\Invoices\ReadModel\InvoiceReadModelRepository;

final class InvoiceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(InvoiceReadModelRepository::class, InvoiceReadModelDbRepository::class);
        $this->app->singleton(InvoiceRepository::class, InvoiceDbRepository::class);
    }

    /** @return array<class-string> */
    public function provides(): array
    {
        return [
            InvoiceReadModelRepository::class,
            InvoiceReadModelDbRepository::class,
        ];
    }
}
