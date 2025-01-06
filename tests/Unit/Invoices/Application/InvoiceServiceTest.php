<?php

declare(strict_types=1);

namespace Tests\Unit\Invoices\Application;

use Generator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Invoices\Application\Command\AddInvoice;
use Modules\Invoices\Application\InvoiceService;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\InvoiceRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class InvoiceServiceTest extends TestCase
{
    use WithFaker;

    private InvoiceRepository $repository;
    private Dispatcher $dispatcher;

    private InvoiceService $service;

    protected function setUp(): void
    {
        $this->setUpFaker();

        $this->repository = $this->createMock(InvoiceRepository::class);
        $this->dispatcher = $this->createMock(Dispatcher::class);

        $this->service = new InvoiceService(
           $this->repository,
            $this->dispatcher,
        );
    }

    #[DataProvider('invoiceValidDataProvider')]
    public function testAddInvoice($data): void
    {
        $this
            ->repository
            ->expects($this->once())
            ->method('addInvoice')
            ->with(
                $data['customer_name'],
                $data['customer_email'],
                StatusEnum::Draft
            );
        $this->service->addInvoice(
            new AddInvoice($data['customer_name'], $data['customer_email'], $data['product_lines'])
        );
    }

    public static function invoiceValidDataProvider(): Generator
    {
        yield 'valid invoice data with empty product lines' => [
            [
                'customer_name' => 'John Doe',
                'customer_email' => 'john@doe.com',
                'product_lines' => null,
            ]
         ];

        yield 'valid invoice data with product lines' => [
            [
                'customer_name' => 'John Doe',
                'customer_email' => 'john@doe.com',
                'product_lines' => collect(
                    [
                        [
                            'name' => 'Product 1',
                            'quantity' => 1,
                            'price' => 1000,
                        ],
                        [
                            'name' => 'Product 2',
                            'quantity' => 5,
                            'price' => 2000,
                        ]
                    ]
                ),
                ]
            ];
    }
}
