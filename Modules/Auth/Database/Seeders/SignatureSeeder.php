<?php

namespace Modules\Auth\Database\Seeders;
use Illuminate\Database\Seeder;
use Modules\Auth\Entities\Document; 
use Modules\Auth\Entities\Signature; 
use App\Models\Core\User;

class SignatureSeeder extends Seeder
{
    public function run(): void
    {
        $documents = Document::all();
        $users = User::all();

        foreach ($documents as $document) {
            // Ambil 2 user acak selain pemilik dokumen untuk jadi penandatangan
            $signers = $users->where('id', '!=', $document->user_id)->random(2);

            foreach ($signers as $signer) {
                Signature::create([
                    'document_id' => $document->id,
                    'signer_id' => $signer->id,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
