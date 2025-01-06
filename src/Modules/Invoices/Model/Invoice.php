<?php

declare(strict_types=1);

namespace Modules\Invoices\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasUuids;

    protected $table = 'invoices';

    protected $fillable = ['id', 'customer_name', 'customer_email', 'status'];

    public function invoiceProductLines(): HasMany
    {
        return $this->hasMany(InvoiceProductLine::class);
    }
}
