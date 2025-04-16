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
     Schema::create('member', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->string('nama_member', 50);
    $table->integer('kontak');
    $table->string('email', 30);
    $table->string('alamat', 200);
    $table->date('tanggal_bergabung');
    $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
    $table->softDeletes(); 

    $table->foreign('user_id')->references('id')->on('user');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'member');
    }
};