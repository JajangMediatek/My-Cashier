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
            $table->bigInteger('qty');
            $table->decimal('harga_beli', 14, 2);
            $table->decimal('sub_total', 14, 2);
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
