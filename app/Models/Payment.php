<?php

namespace App\Models;

class Payment extends Model
{
    const OPEN = 'open';
    const PENDING = 'pending';
    const PAID = 'paid';
    const IN_STORE = 'in_store';
    const REFUNDED = 'refunded';

    protected $table = 'payments';
}