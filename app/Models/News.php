<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use HasFactory, SoftDeletes;

    // Tentukan nama tabel yang digunakan jika nama tabel tidak sesuai dengan konvensi Laravel
    protected $table = 'news';

    // Tentukan kolom yang dapat diisi (fillable) untuk mencegah mass-assignment vulnerability
    protected $fillable = [
        'title',        // Judul berita (array JSON)
        'slug',         // Slug berita
        'excerpt',      // Ringkasan berita (array JSON)
        'content',      // Isi berita (array JSON)
        'media_url',    // URL media (gambar atau video terkait berita)
        'author',       // Penulis berita (array JSON)
        'is_highlight', // Status berita highlight
        'type',         // (podcast atau testimonial)
        'type_url',     // URL terkait jenis berita
        'tags',         // Tag berita (array JSON)
        'published_at', // Waktu publikasi berita (array JSON atau timestamp)
    ];


    /**
     * Mengambil berita yang sudah diterbitkan
     */
    public static function published()
    {
        return self::whereNotNull('published_at')->where('published_at', '<=', now())->get();
    }

    /**
     * Menambahkan aksesors dan mutators, misalnya untuk format tanggal
     */
    public function getPublishedAtAttribute($value)
    {
        return $this->parseJsonDate($value);
    }

    public function setPublishedAtAttribute($value)
    {
        $this->attributes['published_at'] = $this->formatJsonDate($value);
    }

    /**
     * Fungsi untuk parsing tanggal dari JSON atau format lainnya
     */
    protected function parseJsonDate($value)
    {
        if ($value) {
            return \Carbon\Carbon::parse($value)->format('d-m-Y H:i');
        }
        return null;
    }

    /**
     * Fungsi untuk memformat tanggal ke dalam format yang sesuai
     */
    protected function formatJsonDate($value)
    {
        if ($value) {
            return \Carbon\Carbon::parse($value)->toDateTimeString();
        }
        return null;
    }

    // Model News.php
    public function getTagsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setTagsAttribute($value)
    {
        $this->attributes['tags'] = json_encode($value);
    }

    public function getAuthorAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setAuthorAttribute($value)
    {
        $this->attributes['author'] = json_encode($value);
    }

    public function getMetaDataAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setMetaDataAttribute($value)
    {
        $this->attributes['meta_data'] = json_encode($value);
    }

}
