<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerDetail extends Model
{
    protected $fillable = [
        'customer_id',
        'first_name',
        'last_name',
        'address_line_one',
        'address_line_two',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
