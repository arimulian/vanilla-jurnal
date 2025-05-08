<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $table = 'sales';
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'no_sales',
        'date',
        'total_amount',
        'discount',
        'tax',
        'final_amount',
        'status',
        'payment_method',
        'branch_id'
    ];


    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function salesItems()
    {
        return $this->hasMany(SalesItem::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->no_sales = 'INV-' . date('Ymd') . '-' . str_pad($model->count() + 1, 4, '0', STR_PAD_LEFT);
            $model->date = now()->format('Ymd');
        });
    }
}
