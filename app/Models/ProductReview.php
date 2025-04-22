<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $fillable = [
        'product_id',
        'customer_id',
        'rating',
        'description',
    ];

    public function profile()
    {
        return $this->belongsTo(CustomerProfile::class, 'customer_id');
    }
}
