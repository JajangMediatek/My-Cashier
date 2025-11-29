<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembuanganBarang extends Model
{
     protected $guarded = ['id'];

    public static function nomorPembuangan(){
        // TRS-2305250001
        $max = self::max('id');
        $prefix = 'TRS-';
        $date = date('dmy');
        $nomor = $prefix . $date . str_pad($max + 1, 4, '0', STR_PAD_LEFT);
        return $nomor;
    }

    public function items(){
        return $this->hasMany(ItemPembuanganBarang::class, 'nomor_pembuangan', 'nomor_pembuangan');
    }
}
