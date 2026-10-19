<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bukus', function (Blueprint $table) {
            $table->string('isbn')->nullable()->after('penerbit');
            $table->integer('jumlah_halaman')->nullable()->after('tahun_terbit');
            $table->string('inisial', 8)->nullable()->after('judul');
            $table->string('cover')->nullable()->after('inisial');
            $table->unsignedInteger('populer')->default(0)->after('cover');
            $table->unsignedInteger('jumlah_favorit')->default(0)->after('populer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bukus', function (Blueprint $table) {
            $table->dropColumn([
                'isbn',
                'jumlah_halaman',
                'inisial',
                'cover',
                'populer',
                'jumlah_favorit',
            ]);
        });
    }
};
