<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Products extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'products';

    protected $fillable = [
        'sku', 'name', 'product_image', 'description', 'price', 'qty', 'status'
    ];
}
