<?php

declare(strict_types=1);

namespace Modules\Invoices\Application;

use Exception;
use Modules\Invoices\Application\Command\AddInvoice;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\InvoiceRepository;

final readonly class InvoiceService
{
    public function __construct(private InvoiceRepository $repository)
    {
    }

    /**
     * @throws Exception
     */
    public function addInvoice(AddInvoice $command): void
    {

        $this->assertPricesValid($command);
        $this->assertQuantitiesValid($command);

        $this->repository->addInvoice(
            $command->getCustomerName(),
            $command->getCustomerEmail(),
            StatusEnum::Draft,
            $command->getInvoiceProductLines()
        );

        // add invoice to db

        // throw an event
    }

    private function assertPricesValid(AddInvoice $command): void
    {
        if ($command->getInvoiceProductLines() !== null) {
            foreach ($command->getInvoiceProductLines() as $productLine) {
                if ($productLine['price'] < 0) {
                    throw new Exception('Price should be greater than 0');
                }
            }
        }
    }

    private function assertQuantitiesValid(AddInvoice $command): void
    {
        if ($command->getInvoiceProductLines() !== null) {
            foreach ($command->getInvoiceProductLines() as $productLine) {
                if ($productLine['quantity'] < 0) {
                    throw new Exception('Quantity should be greater than 0');
                }
            }
        }
    }

}
