<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'name',
        'slug',
        'category_type',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // public function getRouteKeyName()
    // {
    //     return 'slug';
    // }
}
