<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Categories extends Model
{
     protected $fillable = ['category_name', 'description'];

     public function products()
     {
         return $this->hasMany(Product::class, 'category_id');
     }
}
