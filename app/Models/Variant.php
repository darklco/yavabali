<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Variant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'variant';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'product_id',
        'size',
        'image_front',
        'image_back',
    ];

    protected $casts = [
        'size' => 'array',
        'image_front' => 'array',
        'image_back' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
