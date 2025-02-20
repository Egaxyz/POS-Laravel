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
        Schema::create('detail_pembelian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembelian_id');
            $table->unsignedBigInteger('bahan_baku_id');
            $table->integer('jumlah');
            $table->integer('harga_satuan');

            $table->foreign('bahan_baku_id')->references('id')->on('bahan_baku');
            $table->foreign('pembelian_id')->references('id')->on('pembelian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'detail_pembelian');
    }
};