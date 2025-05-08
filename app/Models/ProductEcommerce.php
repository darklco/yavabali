<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductEcommerce extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'variant_id',
        'title',
        'icon',
        'link',
        'type'
    ];

    /**
     * Get the variant that owns the ecommerce link.
     */
    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }
}