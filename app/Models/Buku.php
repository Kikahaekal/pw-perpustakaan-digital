<?php

namespace App\Models;

use Database\Factories\BukuFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Buku extends Model
{
    /** @use HasFactory<BukuFactory> */
    use HasFactory;

    /**
     * Pilihan section/rak yang tersedia di form admin.
     *
     * @var array<string, string>
     */
    public const SEKSI_LABELS = [
        'new_uploads' => 'New Uploads',
        'highly_requested' => 'Highly Requested',
        'popular_now' => 'Popular Now',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'judul',
        'inisial',
        'cover',
        'cover_image',
        'seksi',
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

    /**
     * URL publik untuk cover hasil unggahan admin.
     */
    protected function coverUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->cover_image
            ? Storage::disk('public')->url($this->cover_image)
            : null);
    }

    /**
     * Label section yang siap ditampilkan.
     */
    public function seksiLabel(): ?string
    {
        return self::SEKSI_LABELS[$this->seksi] ?? null;
    }
}
