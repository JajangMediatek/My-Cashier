<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenerimaanBarang extends Model
{
    protected $guarded = ['id'];

    public static function nomorPenerimaan(){
        // PBR-2305250001
        $max = self::max('id');
        $prefix = 'PBR-';
        $date = date('dmy');
        $nomor = $prefix . $date . str_pad($max + 1, 4, '0', STR_PAD_LEFT);
        return $nomor;
    }

    public function items(){
        return $this->hasMany(itemPenerimaanBarang::class, 'nomor_penerimaan', 'nomor_penerimaan');
    }
}
