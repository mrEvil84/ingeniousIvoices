<?php

declare(strict_types=1);

namespace Tests\Unit\Invoices\Application;

use Exception;
use Generator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Modules\Invoices\Application\Command\AddInvoice;
use Modules\Invoices\Application\InvoiceService;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\InvoiceRepository;
use Modules\Invoices\Entity\Invoice;
use Modules\Invoices\Entity\InvoiceProductLine;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private InvoiceRepository $repository;
    private Dispatcher $dispatcher;
    private InvoiceService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpFaker();

        $this->repository = $this->createMock(InvoiceRepository::class);
        $this->dispatcher = $this->createMock(Dispatcher::class);

        $this->service = new InvoiceService(
            $this->repository,
            $this->dispatcher,
        );
    }

    #[DataProvider('invoiceValidDataProvider')]
    public function testAddInvoice(string $customerName, string $customerEmail, ?Collection $productLines = null): void
    {
        $this
            ->repository
            ->expects($this->once())
            ->method('addInvoice')
            ->with(
                $customerName,
                $customerEmail,
                StatusEnum::Draft,
            );
        $this->service->addInvoice(
            new AddInvoice($customerName, $customerEmail, $productLines)
        );
    }

    public function testSendInvoice(): void
    {
        $invoiceId = $this->faker->uuid;
        $customerName = 'John Doe';
        $customerEmail = 'john@doe.com';
        $status = StatusEnum::Draft;

        $productLines = new Collection();
        $productLines->add(
            new InvoiceProductLine($this->faker->uuid, $invoiceId, 'Product1', 1000, 2)
        );
        $productLines->add(
            new InvoiceProductLine($this->faker->uuid, $invoiceId, 'Product1', 2000, 3)
        );

        $invoice = new Invoice($invoiceId, $customerName, $customerEmail, $status, $productLines);

        $this
            ->repository
            ->expects($this->once())
            ->method('invoiceExists')
            ->with($invoiceId)->willReturn(true);

        $this
            ->repository
            ->expects($this->once())
            ->method('getInvoiceEntityById')
            ->with($invoiceId)
            ->willReturn($invoice);

        $this
            ->repository
            ->expects($this->once())
            ->method('setInvoiceStatus')
            ->with($invoiceId, StatusEnum::Sending);


        $this->dispatcher->expects($this->once())->method('dispatch');

        $this->service->sendInvoice($invoiceId);
    }

    #[DataProvider('sendInvoiceInvalidDataProvider')]
    public function testSendInvoiceFailedWhenInvalidStatus(
        string $customerName,
        string $customerEmail,
        StatusEnum $status,
        Collection $productLines,
        string $expectedExceptionMessage,
    ): void {
        $invoiceId = $this->faker->uuid;
        $invoice = new Invoice($invoiceId, $customerName, $customerEmail, $status, $productLines);

        $this
            ->repository
            ->expects($this->once())
            ->method('invoiceExists')
            ->with($invoiceId)->willReturn(true);

        $this
            ->repository
            ->expects($this->once())
            ->method('getInvoiceEntityById')
            ->with($invoiceId)
            ->willReturn($invoice);

        $this->expectExceptionMessage(sprintf($expectedExceptionMessage, $invoiceId));
        $this->expectException(Exception::class);

        $this->service->sendInvoice($invoiceId);
    }

    public function testSetStatusSentToClient(): void
    {
        $invoiceId = $this->faker->uuid;
        $customerName = 'John Doe';
        $customerEmail = 'john@doe.com';
        $status = StatusEnum::Sending;
        $invoice = new Invoice($invoiceId, $customerName, $customerEmail, $status, new Collection());

        $this
            ->repository
            ->expects($this->once())
            ->method('invoiceExists')
            ->with($invoiceId)->willReturn(true);

        $this
            ->repository
            ->expects($this->once())
            ->method('getInvoiceEntityById')
            ->with($invoiceId)
            ->willReturn($invoice);

        $this
            ->repository
            ->expects($this->once())
            ->method('setInvoiceStatus')
            ->with($invoiceId, StatusEnum::SentToClient);

        $this->service->setStatusSentToClient($invoiceId);
    }

    public function testSetStatusSentToClientFailWhenInvoiceNotExists(): void
    {
        $invoiceId = $this->faker->uuid;

        $this
            ->repository
            ->expects($this->once())
            ->method('invoiceExists')
            ->with($invoiceId)->willReturn(false);

        $this->expectExceptionMessage(sprintf('Invoice %s not exists.', $invoiceId));
        $this->expectException(Exception::class);
        $this->service->setStatusSentToClient($invoiceId);
    }

    public function testSetStatusSentToClientFailWhenInvalidInvoiceStatus(): void
    {
        $invoiceId = $this->faker->uuid;
        $customerName = 'John Doe';
        $customerEmail = 'john@doe.com';
        $status = StatusEnum::Draft;
        $invoice = new Invoice($invoiceId, $customerName, $customerEmail, $status, new Collection());

        $this
            ->repository
            ->expects($this->once())
            ->method('invoiceExists')
            ->with($invoiceId)->willReturn(true);

        $this
            ->repository
            ->expects($this->once())
            ->method('getInvoiceEntityById')
            ->with($invoiceId)
            ->willReturn($invoice);

        $this->expectExceptionMessage(sprintf('Invoice %s have not sending status.', $invoice->getId()));
        $this->expectException(Exception::class);
        $this->service->setStatusSentToClient($invoiceId);
    }

    public static function sendInvoiceInvalidDataProvider(): Generator
    {
        yield 'invalid invoice status' => [
            'customerName' => 'John Doe',
            'customerEmail' => 'john@doe.com',
            'status' => StatusEnum::Sending,
            'productLines' => collect(
                [
                    [
                        'name' => 'Product 1',
                        'quantity' => 0,
                        'price' => 1000,
                    ],
                    [
                        'name' => 'Product 2',
                        'quantity' => -1,
                        'price' => 2000,
                    ]
                ]
            ),
            'expectedExceptionMessage' => 'Invoice %s is not draft.',
        ];

        yield 'product lines contains invalid quantity ' => [
            'customerName' => 'John Doe',
            'customerEmail' => 'john@doe.com',
            'status' => StatusEnum::Draft,
            'productLines' => collect(
                [
                    new InvoiceProductLine(
                        '1234',
                        '1234',
                        'Product1',
                        1000,
                        -10
                    )
                ]
            ),
            'expectedExceptionMessage' => 'Invoice %s contains Invoice product lines with invalid price or quantity.',
        ];

        yield 'product lines contains invalid price ' => [
            'customerName' => 'John Doe',
            'customerEmail' => 'john@doe.com',
            'status' => StatusEnum::Draft,
            'productLines' => collect(
                [
                    new InvoiceProductLine(
                        '1234',
                        '1234',
                        'Product1',
                        0,
                        1
                    )
                ]
            ),
            'expectedExceptionMessage' => 'Invoice %s contains Invoice product lines with invalid price or quantity.',
        ];

        yield 'invoice not contains invoice product lines ' => [
            'customerName' => 'John Doe',
            'customerEmail' => 'john@doe.com',
            'status' => StatusEnum::Draft,
            'productLines' => collect(
            ),
            'expectedExceptionMessage' => 'Invoice %s has no product lines.',
        ];
    }

    #[DataProvider('invoiceNotValidDataProvider')]
    public function testNotAddInvoiceWhenInvalidProductLinesData(
        string $customerName,
        string $customerEmail,
        Collection $productLines,
        string $expectedExceptionMessage,
    ): void {
        $this->expectException(exception: Exception::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->service->addInvoice(
            new AddInvoice($customerName, $customerEmail, $productLines)
        );
    }

    public static function invoiceValidDataProvider(): Generator
    {
        yield 'valid invoice data with empty product lines' => [

            'customerName' => 'John Doe',
            'customerEmail' => 'john@doe.com',
            'productLines' => null,
        ];

        yield 'valid invoice data with product lines' => [

            'customerName' => 'John Doe',
            'customerEmail' => 'john@doe.com',
            'productLines' => collect(
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

        ];
    }

    public static function invoiceNotValidDataProvider(): Generator
    {
        yield 'invalid invoice data - invalid product lines quantity ' => [

            'customerName' => 'John Doe',
            'customerEmail' => 'john@doe.com',
            'productLines' => collect(
                [
                    [
                        'name' => 'Product 1',
                        'quantity' => 0,
                        'price' => 1000,
                    ],
                    [
                        'name' => 'Product 2',
                        'quantity' => -1,
                        'price' => 2000,
                    ]
                ]
            ),
            'expectedExceptionMessage' => 'Quantity should be greater than 0',
        ];

        yield 'invalid invoice data - invalid product lines price ' => [

            'customerName' => 'John Doe',
            'customerEmail' => 'john@doe.com',
            'productLines' => collect(
                [
                    [
                        'name' => 'Product 1',
                        'quantity' => 0,
                        'price' => -1000,
                    ],
                    [
                        'name' => 'Product 2',
                        'quantity' => -1,
                        'price' => 0,
                    ]
                ]
            ),
            'expectedExceptionMessage' => 'Price should be greater than 0',
        ];
    }
}
