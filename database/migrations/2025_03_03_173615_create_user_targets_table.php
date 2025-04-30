<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTargetsTable extends Migration
{
    public function up()
    {
        Schema::create('user_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('program_id');
            $table->date('date');
            $table->decimal('value', 10, 2)->default(0); // Untuk target angka
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->unique(['user_id', 'program_id', 'date']); // Pastikan tidak ada duplikat
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_targets');
    }
}
