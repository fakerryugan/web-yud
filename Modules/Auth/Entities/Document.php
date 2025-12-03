<?php

namespace Modules\Auth\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes; // <--- 1. Import SoftDeletes
use App\Models\Core\User;

class Document extends Model
{
    use HasFactory, SoftDeletes; // <--- 2. Pasang Trait SoftDeletes

    protected $guarded = [];

    // Factory untuk testing
    protected static function newFactory()
    {
        return \Modules\Auth\Database\factories\DocumentFactory::new();
    }

    // --- RELASI WAJIB ---
    
    // Relasi ke Pemilik Dokumen (User)
    // Controller memanggil $document->owner
  public function owner()
{
    return $this->belongsTo(\App\Models\Core\User::class, 'user_id');
}

public function signatures()
{
    return $this->hasMany(Signature::class);
}
}