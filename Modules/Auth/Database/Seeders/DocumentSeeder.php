<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Core\User;
use Modules\Auth\Entities\Document; 
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $filename = 'contoh_dokumen_' . $user->id . '.pdf';
            $encryptedFilename = Crypt::encryptString($filename);

            Document::create([
                'user_id' => $user->id,
                'file_path' => 'documents/' . $filename,
                'encrypted_original_filename' => $encryptedFilename,
                'tujuan' => 'Contoh Tujuan Dokumen oleh ' . $user->name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
