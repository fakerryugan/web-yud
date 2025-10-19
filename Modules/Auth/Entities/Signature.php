<?php

namespace Modules\Auth\Entities; 
use App\Models\Core\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- TAMBAHKAN INI
use Modules\Auth\Database\factories\SignatureFactory; // <-- TAMBAHKAN INI

class Signature extends Model
{
    use HasFactory; // <-- TAMBAHKAN INI

    protected $fillable = [
        'document_id',
        'signer_id',
        'status',
        'signed_at',
        'sign_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'signer_id');
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    // TAMBAHKAN METHOD INI UNTUK MENUNJUK KE FACTORY YANG BENAR
    protected static function newFactory()
    {
        return SignatureFactory::new();
    }
}