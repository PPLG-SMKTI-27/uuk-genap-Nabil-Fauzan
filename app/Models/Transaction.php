<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TransactionDetail;

class Transaction extends Model
{
     protected $fillable = ['transaction_no', 'date', 'customer_name', 'total_amount', 'status'];

     public function transactionDetails()
     {
         return $this->hasMany(TransactionDetail::class);
     }
}
