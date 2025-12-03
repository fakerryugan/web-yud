<?php

namespace Modules\Auth\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\User;

class Signature extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return \Modules\Auth\Database\factories\SignatureFactory::new();
    }

    // --- RELASI WAJIB ---

    // Controller memanggil $signature->document
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    // Controller memanggil $signature->user (untuk ambil nama signer)
    public function user() // atau signer() tergantung foreign key
    {
        // Pastikan foreign key sesuai database Anda, biasanya 'signer_id'
        return $this->belongsTo(User::class, 'signer_id');
    }
}