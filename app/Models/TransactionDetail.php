<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use app\Models\Transaction;

class TransactionDetail extends Model
{
    protected $fillable = ['quantity', 'price', 'subtotal'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
