<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductEcommerce extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products_ecommerces'; // <-- ini penting

    protected $fillable = [
        'variant_id',
        'title',
        'icon',
        'link',
        'type',
    ];
}
