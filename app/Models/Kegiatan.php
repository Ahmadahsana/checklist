<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    // protected $fillable = ['nama_kegiatan', 'tanggal', 'jenis', 'kode_unik'];

    protected $guarded = ['id'];

    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }

    public function peserta()
    {
        return $this->belongsToMany(User::class, 'presensis');
    }

    // Hitung persentase kehadiran
    public function getPersentaseKehadiranAttribute()
    {
        $totalPeserta = User::where('role', 'user')->count();
        $hadir = $this->presensis()->where('hadir', true)->count();

        return $totalPeserta > 0 ? round(($hadir / $totalPeserta) * 100, 2) : 0;
    }

    // Hitung persentase ketidakhadiran
    public function getPersentaseKetidakhadiranAttribute()
    {
        return 100 - $this->persentase_kehadiran;
    }
}
