<?php

namespace Modules\Auth\Entities; 

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\User; 
class Document extends Model
{
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

}
