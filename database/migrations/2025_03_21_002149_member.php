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
    Schema::create('ajukan_menu', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->string('nama_makanan', 50);
    $table->string('harga', 20)->default(0);
    $table->string('stok', 100)->default(0);
    $table->enum('kategori', ['makanan', 'minuman', 'snack']);
    $table->string('deskripsi', 200)->nullable();
    $table->date('tanggal')->nullable();
    $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending'); // Status pengajuan

    $table->foreign('user_id')->references('id')->on('user');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'ajukan_menu');
    }
};