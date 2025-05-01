<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressBulanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bulan',
        'total_persentase',
        'jumlah_record',
        'value',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
