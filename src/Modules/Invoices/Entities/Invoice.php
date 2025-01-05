<?php

declare(strict_types=1);

namespace Modules\Invoices\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $table = 'invoices';

    public function invoiceProductLines(): HasMany
    {
        return $this->hasMany(InvoiceProductLine::class);
    }
}
