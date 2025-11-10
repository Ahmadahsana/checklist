<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = ['nama_program', 'target', 'level', 'type', 'unit'];

    public function userTargets()
    {
        return $this->hasMany(UserTarget::class);
    }
}
