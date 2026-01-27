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
    // Generate kode unik per minggu untuk kegiatan rutin
    public function getKodeMingguanAttribute()
    {
        if ($this->jenis === 'rutin') {
            // Gunakan tahun dan minggu saat ini sebagai seed
            // Format seed: KODE_ASLI-TAHUN-MINGGU
            $seed = $this->kode_unik . '-' . now()->year . '-' . now()->weekOfYear;

            // Hash seed dan ambil 6 karakter pertama, uppercase
            return strtoupper(substr(md5($seed), 0, 6));
        }

        return $this->kode_unik;
    }
}
