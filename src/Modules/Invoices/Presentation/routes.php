<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;
use Modules\Invoices\Presentation\Http\InvoiceController;
//use Ramsey\Uuid\Validator\GenericValidator;

//Route::pattern('action', '^[a-zA-Z]+$');
//Route::pattern('reference', (new GenericValidator)->getPattern());



Route::get('/invoice/view/{invoiceId}', [InvoiceController::class, 'view'])->name('invoice.view');
