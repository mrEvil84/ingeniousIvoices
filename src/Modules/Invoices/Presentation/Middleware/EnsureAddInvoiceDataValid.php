<?php

declare(strict_types=1);

namespace Modules\Invoices\Presentation\Middleware;

use Closure;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class EnsureAddInvoiceDataValid extends Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|max:255',
            'customer_email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response([$validator->errors()->toArray()], Response::HTTP_BAD_REQUEST);
        }

        if (
            array_key_exists('invoice_product_lines', $request->all())
            && !empty($request->all()['invoice_product_lines'])
        ) {
            foreach ($request->all()['invoice_product_lines'] as $line) {
                $validator = Validator::make($line, [
                    'name' => 'required|max:255',
                    'price' => 'required|int|gt:0',
                    'quantity' => 'required|int|gt:0',
                ]);
                if ($validator->fails()) {
                    return response([$validator->errors()->toArray()], Response::HTTP_BAD_REQUEST);
                }
            }
        }

        return $next($request);
    }
}
