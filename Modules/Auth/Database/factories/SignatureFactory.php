<?php
namespace Modules\Auth\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Core\User;
use Modules\Auth\Entities\Document;
use Illuminate\Support\Str;

class SignatureFactory extends Factory
{
    protected $model = \Modules\Auth\Entities\Signature::class;

    public function definition()
    {
        return [
            'document_id' => Document::factory(),
            'signer_id' => User::factory(),
            'status' => 'pending', // default status
            'signed_at' => null,
            'sign_token' => Str::uuid()->toString(),
        ];
    }
}