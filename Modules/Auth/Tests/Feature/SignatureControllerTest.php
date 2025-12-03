<?php

namespace Modules\Auth\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Core\User;
use Laravel\Sanctum\Sanctum;
use Modules\Auth\Entities\Document;
use Modules\Auth\Entities\Signature;
use Illuminate\Support\Str;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Contract\Messaging;

class SignatureControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $messagingMock = \Mockery::mock(Messaging::class);
        $messagingMock->shouldReceive('send')->andReturnNull();
        Firebase::shouldReceive('messaging')->andReturn($messagingMock);
    }

    /** @test */
    public function pengguna_bisa_menambahkan_penandatangan_ke_dokumen()
    {
        $documentOwner = User::factory()->create(['username' => 'owner_sign']);
        Sanctum::actingAs($documentOwner);
        
        $token = Str::uuid()->toString();
        $document = Document::factory()->create([
            'user_id' => $documentOwner->id, 
            'tujuan' => null,
            'access_token' => $token,
            'status' => 'pending'
        ]);
        
        $signerUser = User::factory()->create([
            'nip' => '199001012020121001',
            'username' => 'signer_one'
        ]);

        $response = $this->postJson("/api/documents/{$token}/signer", [
            'nip' => '199001012020121001',
            'tujuan' => 'Persetujuan perjalanan dinas', 
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
    }
    
    /** @test */
    public function penandatangan_bisa_menyetujui_permintaan_tanda_tangan()
    {
        $signerUser = User::factory()->create(['username' => 'signer_two']);
        // PENTING: Owner harus ada agar notifikasi tidak error
        $owner = User::factory()->create(['username' => 'owner_two']);
        
        $document = Document::factory()->create([
            'user_id' => $owner->id, 
            'status' => 'pending'
        ]);
        
        $signature = Signature::factory()->create([
            'document_id' => $document->id,
            'signer_id' => $signerUser->id,
            'status' => 'pending',
            'sign_token' => Str::uuid()->toString()
        ]);
        
        Sanctum::actingAs($signerUser);

        $response = $this->postJson("/api/documents/signature/{$signature->sign_token}", [
            'status' => 'approved',
        ]);

        // Debugging: Jika masih 500, uncomment ini untuk lihat errornya
        // dd($response->json()); 

        $response->assertStatus(200);
        $this->assertDatabaseHas('signatures', ['id' => $signature->id, 'status' => 'approved']);
    }
    
    /** @test */
    public function penandatangan_bisa_menolak_permintaan_tanda_tangan()
    {
        $signerUser = User::factory()->create(['username' => 'signer_three']);
        $owner = User::factory()->create(['username' => 'owner_three']);
        
        $document = Document::factory()->create(['user_id' => $owner->id, 'status' => 'pending']);
        
        $signature = Signature::factory()->create([
            'document_id' => $document->id,
            'signer_id' => $signerUser->id,
            'status' => 'pending',
            'sign_token' => Str::uuid()->toString()
        ]);

        Sanctum::actingAs($signerUser);

        $response = $this->postJson("/api/documents/signature/{$signature->sign_token}", [
            'status' => 'rejected',
            'comment' => 'Tanda tangan jelek.' 
        ]);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('signatures', ['id' => $signature->id, 'status' => 'rejected']);
    }
}