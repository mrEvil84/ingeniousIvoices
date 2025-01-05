<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;
use Modules\Invoices\Presentation\Http\InvoiceController;
use Modules\Invoices\Presentation\Middleware\EnsureAddInvoiceDataValid;

//use Ramsey\Uuid\Validator\GenericValidator;

//Route::pattern('action', '^[a-zA-Z]+$');
//Route::pattern('reference', (new GenericValidator)->getPattern());



Route::get('/invoice/{invoiceId}', [InvoiceController::class, 'getInvoice'])->name('invoice.view');

Route::post('/invoice', [InvoiceController::class, 'addInvoice'])
    ->name('invoice.add')
    ->middleware(EnsureAddInvoiceDataValid::class);
