<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';

    protected $fillable = [
        'aksi',
        'tabel',
        'user_id',
        'keterangan',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
