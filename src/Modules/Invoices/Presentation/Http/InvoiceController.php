<?php

declare(strict_types=1);

namespace Modules\Invoices\Presentation\Http;

use Illuminate\Http\JsonResponse;
use Modules\Invoices\ReadModel\InvoiceReadModel;
use Symfony\Component\HttpFoundation\Response;

final readonly class InvoiceController
{
    public function __construct(private InvoiceReadModel $invoiceReadModel)
    {
    }

    public function view(int $invoiceId): JsonResponse
    {
        $data = $this->invoiceReadModel->getInvoice($invoiceId);

        return new JsonResponse(data: $data->toArray(), status: Response::HTTP_OK);
    }
}
