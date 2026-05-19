<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TransactionDetail;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price', 'category_id'];

    public function category()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
