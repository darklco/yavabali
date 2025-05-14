<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'types',
        'title',
        'slug',
        'description',
        'nutrient'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
    'title' => 'array',
    'description' => 'array',
    'types' => 'array', // jika types juga disimpan sebagai JSON
];


    /**
     * Get the variants for the product.
     */
    public function variants()
    {
        return $this->hasMany(Variant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

     public function ingredients()
    {
        return $this->hasMany(ingredient::class);
    }
}
