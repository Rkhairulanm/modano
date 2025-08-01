<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TOut extends Model
{
    protected $table = 't_out';

    protected $fillable = [
        'nomor',
        'tanggal',
        'no_bpb',
        'keterangan',
        'penanggung_jawab',
        'barang_id',
        'jumlah',
        'harga',
        'pengguna'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
