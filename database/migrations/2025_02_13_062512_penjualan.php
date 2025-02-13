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
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('no_faktur', 25);
            $table->integer('total_harga');
            $table->dateTime('tanggal');
            $table->enum('metode_pembayaran', ['Cash', 'Qris', 'Bank']);
            $table->enum('status_penjualan', ['Selesai', 'Proses', 'Gagal']);

            $table->foreign('user_id')->references('id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'penjualan');
    }
};