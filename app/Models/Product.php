<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $keyType = 'int';

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'description',
        'price',
        'cost_price',
        'stock',
        'category_id',
        'sku',
        'barcode',
        'image',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function salesItems()
    {
        return $this->hasMany(SalesItem::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->slug = \Illuminate\Support\Str::slug($model->name);
        });
    }
}
