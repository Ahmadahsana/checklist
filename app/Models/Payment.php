<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // protected $casts = [
    //     'amount' => 'decimal:2',
    //     'payment_date' => 'date',
    //     'due_date' => 'date',
    // ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment_details()
    {
        return $this->hasMany(PaymentDetail::class);
    }
}
