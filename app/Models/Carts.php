<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Carts extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'carts';

    protected $fillable = [
        'product_list', 'total', 'is_checkout'
    ];
}
