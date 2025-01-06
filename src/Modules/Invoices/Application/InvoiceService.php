<?php

declare(strict_types=1);

namespace Modules\Invoices\Application;

use Exception;
use Modules\Invoices\Application\Command\AddInvoice;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\InvoiceRepository;
use Modules\Invoices\Entities\Invoice;
use Modules\Invoices\Entities\InvoiceProductLine;
use Modules\Invoices\Events\InvoiceSending;
use Illuminate\Contracts\Events\Dispatcher;

final readonly class InvoiceService
{
    public function __construct(
        private InvoiceRepository $repository,
        private Dispatcher $dispatcher,
    ) {
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
    }

    /**
     * @throws Exception
     */
    public function sendInvoice(string $invoiceId): void
    {
        $this->assertInvoiceExists($invoiceId);

        /** @var Invoice $invoice */
        $invoice = $this->repository->getInvoiceById($invoiceId);
        $this->assertInvoiceDraftStatus($invoice);
        $this->assertInvoiceProductLines($invoice);

        $this->dispatcher->dispatch(
            new InvoiceSending(
                $invoiceId,
                $invoice->customer_email,
                'Your invoice',
                'Here your invoice.'
            )
        );

        $this->setInvoiceStatusToSending($invoiceId);
    }

    public function setStatusSentToClient(string $invoiceId): void
    {
        $this->assertInvoiceExists($invoiceId);
        $invoice = $this->repository->getInvoiceById($invoiceId);
        $this->assertInvoiceSendingStatus($invoice);

        $this->repository->setInvoiceStatus($invoiceId, StatusEnum::SentToClient);
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

    private function assertInvoiceExists(string $invoiceId): void
    {
        if (!$this->repository->invoiceExists($invoiceId)) {
            throw new Exception(sprintf('Invoice %s not exists.', $invoiceId));
        }
    }

    private function assertInvoiceDraftStatus(Invoice $invoice): void
    {
        if ($invoice->status !== StatusEnum::Draft->value) {
            throw new Exception(sprintf('Invoice %s is not draft.', $invoice->id));
        }
    }

    private function assertInvoiceProductLines(Invoice $invoice): void
    {
        if ($invoice->invoiceProductLines()->count() === 0) {
            throw new Exception(sprintf('Invoice %s has no product lines.', $invoice->id));
        }

        $productLines = $invoice->invoiceProductLines()->get();
        /** @var InvoiceProductLine $productLine */
        foreach ($productLines as $productLine) {
            if ($productLine->price <= 0 || $productLine->quantity <= 0) {
                throw new Exception(
                    sprintf(
                        'Invoice %s contains Invoice product lines with invalid price or quantity.',
                        $invoiceId
                    )
                );
            }
        }
    }

    private function setInvoiceStatusToSending(string $invoiceId): void
    {
        $this->repository->setInvoiceStatus($invoiceId, StatusEnum::Sending);
    }

    private function assertInvoiceSendingStatus(Invoice $invoice): void
    {
        if ($invoice->status !== StatusEnum::Sending->value) {
            throw new Exception(sprintf('Invoice %s have not sending status.', $invoice->id));
        }
    }
}
