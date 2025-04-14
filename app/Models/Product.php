<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $keyType = 'int';

    protected $guarded = false;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
