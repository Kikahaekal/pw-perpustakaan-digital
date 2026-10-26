<?php

namespace App\Models;

use Database\Factories\BukuFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buku extends Model
{
    /** @use HasFactory<BukuFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'judul',
        'inisial',
        'cover',
        'pengarang',
        'penerbit',
        'isbn',
        'tahun_terbit',
        'jumlah_halaman',
        'stok',
        'populer',
        'jumlah_favorit',
    ];

    /**
     * @return BelongsToMany<Kategori, $this>
     */
    public function kategoris(): BelongsToMany
    {
        return $this->belongsToMany(Kategori::class, 'buku_kategoris')->withTimestamps();
    }

    /**
     * @return HasMany<Peminjaman, $this>
     */
    public function peminjamans(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }
}
