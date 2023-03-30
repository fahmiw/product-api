<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Orders extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'orders';

    protected $fillable = [
        'fullname', 'address', 'order_items', 'status', 'is_paid', 'grand_total', 'payment_method'
    ];
}
