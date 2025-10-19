<?php

namespace Modules\Auth\Entities; 

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\User; 
use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- TAMBAHKAN INI
use Modules\Auth\Database\factories\DocumentFactory; // <-- TAMBAHKAN INI

class Document extends Model
{
    use HasFactory; // <-- TAMBAHKAN INI

    protected $fillable = [
        'user_id',
        'file_path',
        'encrypted_original_filename',
        'tujuan',
        'access_token', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }

    // TAMBAHKAN METHOD INI UNTUK MENUNJUK KE FACTORY YANG BENAR
    protected static function newFactory()
    {
        return DocumentFactory::new();
    }
}