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
        Schema::create('item_penerimaan_barangs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_penerimaan');
            $table->string('nama_produk');
            $table->string('qty');
            $table->integer('harga_beli');
            $table->integer('sub_total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_penerimaan_barangs');
    }
};
