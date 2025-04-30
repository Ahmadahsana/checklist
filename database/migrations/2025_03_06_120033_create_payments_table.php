<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('harga_kos', 10, 2); // Nominal pembayaran
            $table->integer('max_bayar'); // max bayar angsuran, misal 3 x
            $table->decimal('total_terbayar', 10, 2); // Terbayar
            $table->decimal('kurang', 10, 2); // Kurang
            $table->enum('status', ['belum', 'lunas'])->default('belum');
            $table->date('batas_waktu_angsuran'); // Batas waktu angsuran
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
