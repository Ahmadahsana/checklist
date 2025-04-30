<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->enum('level', ['regular', 'tahfidz'])->nullable();
            $table->string('nama_lengkap');
            $table->string('nama_panggilan')->nullable();
            $table->string('tempat_tanggal_lahir');
            $table->text('alamat_asal');
            $table->string('no_hp');
            $table->string('email')->unique();
            $table->string('prodi');
            $table->string('fakultas');
            $table->string('angkatan');
            $table->string('nama_ayah');
            $table->string('nama_ibu');
            $table->string('no_hp_ortu');
            $table->string('no_hp_saudara_wali')->nullable();
            $table->string('nama_kos')->nullable();
            $table->decimal('harga_kos', 10, 2)->nullable();
            $table->string('foto')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
