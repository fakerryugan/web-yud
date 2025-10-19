<?php

namespace Modules\Auth\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Core\User;
use Laravel\Sanctum\Sanctum;
use Modules\Auth\Entities\Document;
use Modules\Auth\Entities\Signature;

class SignatureControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function pengguna_bisa_menambahkan_penandatangan_ke_dokumen()
    {
        $documentOwner = User::factory()->create();
        Sanctum::actingAs($documentOwner);
        $document = Document::factory()->create(['user_id' => $documentOwner->id, 'tujuan' => null]);
        
        // Pengguna yang akan menandatangani
        $signerUser = User::factory()->create(['nip' => '199001012020121001']);

        $response = $this->postJson("/api/add/{$document->id}", [
            'nip' => '199001012020121001',
            'alasan' => 'Persetujuan perjalanan dinas',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Penandatangan berhasil ditambahkan']);
        
        // Pastikan relasi signature tercipta di database
        $this->assertDatabaseHas('signatures', [
            'document_id' => $document->id,
            'signer_id' => $signerUser->id,
            'status' => 'pending',
        ]);
        
        // Pastikan kolom 'tujuan' di dokumen juga ter-update
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'tujuan' => 'Persetujuan perjalanan dinas',
        ]);
    }
    
    /** @test */
    public function penandatangan_bisa_menyetujui_permintaan_tanda_tangan()
    {
        $signerUser = User::factory()->create();
        $document = Document::factory()->create();
        $signature = Signature::factory()->create([
            'document_id' => $document->id,
            'signer_id' => $signerUser->id,
            'status' => 'pending',
        ]);
        
        // Login sebagai penandatangan
        Sanctum::actingAs($signerUser);

        $response = $this->postJson("/api/documents/signature/{$signature->sign_token}", [
            'status' => 'approved',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Tanda tangan berhasil disetujui']);

        // Pastikan status di database berubah menjadi 'approved'
        $this->assertDatabaseHas('signatures', [
            'id' => $signature->id,
            'status' => 'approved',
        ]);
    }
    
    /** @test */
    public function penandatangan_bisa_menolak_permintaan_tanda_tangan()
    {
        $signerUser = User::factory()->create();
        $document = Document::factory()->create();
        $signature = Signature::factory()->create([
            'document_id' => $document->id,
            'signer_id' => $signerUser->id,
            'status' => 'pending',
        ]);

        Sanctum::actingAs($signerUser);

        $response = $this->postJson("/api/documents/signature/{$signature->sign_token}", [
            'status' => 'rejected',
        ]);
        
        $response->assertStatus(200)
            ->assertJson(['message' => 'Tanda tangan berhasil ditolak']);

        $this->assertDatabaseHas('signatures', [
            'id' => $signature->id,
            'status' => 'rejected',
        ]);
    }
}