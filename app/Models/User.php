<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'role',
        'level',
        'nama_lengkap',
        'nama_panggilan',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat_asal',
        'no_hp',
        'email',
        'prodi',
        'fakultas',
        'angkatan',
        'nama_ayah',
        'nama_ibu',
        'no_hp_ortu',
        'no_hp_saudara_wali',
        'nama_kos',
        'harga_kos',
        'foto',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getAuthIdentifierName()
    {
        return 'username';
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'annual_fee' => 'decimal:2',
    ];





    public function userTargets()
    {
        return $this->hasMany(UserTarget::class);
    }

    public function payments()
    {
        return $this->hasOne(Payment::class);
    }

    public function kos()
    {
        return $this->belongsTo(Kos::class, 'nama_kos', 'id');
    }

    public function getFormattedHargaKosAttribute()
    {
        return 'Rp. ' . number_format($this->harga_kos, 0, ',', '.');
    }
}
