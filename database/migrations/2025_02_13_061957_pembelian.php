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
        Schema::create('pembelian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('bahan_baku_id');
            $table->date('tanggal_pembelian');
            $table->enum('status_pembelian', ['Selesai', 'Pending', 'Gagal']);
            $table->string('gambar');

            
            $table->foreign('bahan_baku_id')->references('id')->on('bahan_baku');
            $table->foreign('user_id')->references('id')->on('user');
            $table->foreign('supplier_id')->references('id')->on('supplier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'pembelian');
    }
};