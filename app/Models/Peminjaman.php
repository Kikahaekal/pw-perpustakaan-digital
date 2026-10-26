<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model
{
    protected $table = 'peminjamen';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'buku_id',
        'tanggal_pinjam',
        'tenggat',
        'tanggal_kembali',
        'status',
        'denda',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'tenggat' => 'date',
            'tanggal_kembali' => 'date',
            'denda' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Buku, $this>
     */
    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class);
    }
}
