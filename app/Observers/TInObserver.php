<?php

namespace App\Observers;

use App\Models\TIn;
use App\Models\Barang;

class TInObserver
{
    /**
     * Handle the TIn "created" event.
     */
    public function created(TIn $tIn): void
    {
        // Update stok barang
        $barang = $tIn->barang;
        if ($barang) {
            $barang->increment('stok', $tIn->jumlah);
        }
    }
    /**
     * Handle the TIn "updated" event.
     */
    public function updated(TIn $tIn): void
    {
        //
    }

    /**
     * Handle the TIn "deleted" event.
     */
    public function deleted(TIn $tIn): void
    {
        // Kurangi stok kalau data dihapus
        $barang = $tIn->barang;
        if ($barang) {
            $barang->decrement('stok', $tIn->jumlah);
        }
    }

    /**
     * Handle the TIn "restored" event.
     */
    public function restored(TIn $tIn): void
    {
        //
    }

    /**
     * Handle the TIn "force deleted" event.
     */
    public function forceDeleted(TIn $tIn): void
    {
        //
    }
}
