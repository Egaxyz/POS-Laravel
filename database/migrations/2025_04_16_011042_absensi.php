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
        Schema::create('absensi', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->date('tanggal');
        $table->time('waktu_masuk')->nullable(); 
        $table->time('waktu_pulang')->nullable(); 
        $table->enum('status', ['hadir', 'sakit', 'cuti']);
        $table->string('keterangan', 200)->nullable();
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('user');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};