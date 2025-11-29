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
        Schema::create('pengeluaran_barangs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengeluaran');
            $table->string('nama_petugas');
            $table->decimal('total_harga', 14, 2);
            $table->decimal('bayar', 14, 2);
            $table->decimal('kembalian', 14, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_barangs');
    }
};
