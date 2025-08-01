<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    public function getStokAttribute()
    {
        $jumlahMasuk = $this->tIn()->sum('jumlah');
        $jumlahKeluar = $this->tOut()->sum('jumlah');

        return $jumlahMasuk - $jumlahKeluar;
    }

    protected $table = 'barang';

    protected $guarded = [];

    // relasi
    public function tIn()
    {
        return $this->hasMany(\App\Models\TIn::class, 'barang_id');
    }

    public function tOut()
    {
        return $this->hasMany(\App\Models\TOut::class, 'barang_id');
    }

    public function kategoris()
    {
        return $this->belongsToMany(Kategori::class, 'barang_kategori');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }


}
