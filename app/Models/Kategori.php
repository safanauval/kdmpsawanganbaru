<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;
    public function parent()
    {
        return $this->belongsTo(Kategori::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Kategori::class, 'parent_id');
    }
    protected $table = 'kategoris';
    protected $fillable = ['nama', 'deskripsi'];

    public function stokBarangs()
    {
        return $this->hasMany(StokBarang::class);
    }
}