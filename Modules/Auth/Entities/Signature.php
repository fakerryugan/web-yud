<?php

namespace Modules\Auth\Entities; 
use App\Models\Core\User;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
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
}
