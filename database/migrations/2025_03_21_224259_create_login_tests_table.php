<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('login_tests', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->string('password'); // Simpan sebagai teks biasa untuk testing
        $table->boolean('status'); // 1 = Berhasil, 0 = Gagal
        $table->text('error_message')->nullable(); // Simpan pesan error jika login gagal
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_tests');
    }
};