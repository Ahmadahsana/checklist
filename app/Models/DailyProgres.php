<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyProgres extends Model
{
    protected $table = 'daily_progres';
    protected $guarded = ['id'];
    use HasFactory;
}
