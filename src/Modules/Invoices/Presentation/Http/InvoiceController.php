<?php

declare(strict_types=1);

namespace Modules\Invoices\Presentation\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Invoices\Application\Command\AddInvoice;
use Modules\Invoices\Application\InvoiceService;
use Modules\Invoices\ReadModel\InvoiceReadModel;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceReadModel $invoiceReadModel,
        private readonly InvoiceService $invoiceService,
    ) {
    }

    public function getInvoice(string $invoiceId): JsonResponse
    {
        $data = $this->invoiceReadModel->getInvoice($invoiceId);

        return new JsonResponse(data: $data->toArray(), status: Response::HTTP_OK);
    }

    public function addInvoice(Request $request): JsonResponse
    {
        try {
            $this->invoiceService->addInvoice(AddInvoice::fromArray($request->toArray()));

            return new JsonResponse(data: [], status: Response::HTTP_CREATED);
        } catch (Throwable $exception) {
            return new JsonResponse(data: [$exception->getMessage()], status: Response::HTTP_BAD_REQUEST);
        }
    }

    public function sendInvoice(string $invoiceId): JsonResponse
    {
        try {
            $this->invoiceService->sendInvoice($invoiceId);

            return new JsonResponse(data: [], status: Response::HTTP_OK);
        } catch (Throwable $exception) {
            return new JsonResponse(data: [$exception->getMessage()], status: Response::HTTP_BAD_REQUEST);
        }
    }
}
