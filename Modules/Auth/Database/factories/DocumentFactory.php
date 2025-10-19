<?php

namespace Modules\Auth\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Core\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class DocumentFactory extends Factory
{
    protected $model = \Modules\Auth\Entities\Document::class;

    public function definition()
    {
        $originalName = $this->faker->word . '.pdf';
        $token = Str::uuid()->toString();
        $nameWithToken = $originalName . '|' . $token;

        return [
            'user_id' => User::factory(),
            'file_path' => 'documents/' . sha1($nameWithToken) . '.pdf',
            'encrypted_original_filename' => Crypt::encryptString($nameWithToken),
            'tujuan' => $this->faker->sentence(),
            'access_token' => $token,
        ];
    }
}