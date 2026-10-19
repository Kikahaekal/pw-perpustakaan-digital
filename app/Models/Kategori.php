<?php

namespace App\Models;

use Database\Factories\KategoriFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kategori extends Model
{
    /** @use HasFactory<KategoriFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nama_kategori',
    ];

    /**
     * @return BelongsToMany<Buku, $this>
     */
    public function bukus(): BelongsToMany
    {
        return $this->belongsToMany(Buku::class, 'buku_kategoris');
    }
}
