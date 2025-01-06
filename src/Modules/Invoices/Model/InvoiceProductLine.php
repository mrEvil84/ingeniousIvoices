<?php

declare(strict_types=1);

namespace Modules\Invoices\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceProductLine extends Model
{
    use HasUuids;

    protected $table = 'invoice_product_lines';

    protected $fillable = ['id', 'invoice_id', 'name', 'price' ,'quantity'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
