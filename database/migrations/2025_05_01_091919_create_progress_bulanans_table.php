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
        Schema::create('progress_bulanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('bulan', 7); // Format YYYY-MM
            $table->float('total_persentase')->default(0);
            $table->integer('jumlah_record')->default(0);
            $table->float('value')->default(0); // Persentase rata-rata
            $table->timestamps();

            // Index untuk query cepat
            $table->index(['user_id', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_bulanans');
    }
};
