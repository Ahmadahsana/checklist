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
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->decimal('jumlah', 10, 2); // Nominal pembayaran
            $table->string('bukti_tf')->nullable(); // Path bukti transfer
            $table->date('tanggal'); // Tanggal pembayaran
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->integer('angsuran_ke'); // Nomor angsuran
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_details');
    }
};
