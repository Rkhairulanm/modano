<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TIn extends Model
{
     protected $table = 't_in';

    protected $fillable = [
        'nomor', 'tanggal', 'surat_jalan', 'no_po', 'keterangan',
        'barang_id', 'jumlah', 'harga', 'supplier', 'job_order', 'total'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
