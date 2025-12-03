<?php

namespace Modules\Auth\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Core\User;
use Laravel\Sanctum\Sanctum;
use Modules\Auth\Entities\Document;
use Modules\Auth\Entities\Signature;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Contract\Messaging;

class DocumentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
        
        $messagingMock = \Mockery::mock(Messaging::class);
        $messagingMock->shouldReceive('send')->andReturnNull();
        Firebase::shouldReceive('messaging')->andReturn($messagingMock);
        
        $this->user = User::factory()->create(['username' => 'testuser_doc']);
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function pengguna_bisa_mengunggah_dokumen()
    {
        $file = UploadedFile::fake()->create('dokumen_tes.pdf', 100);
        $response = $this->postJson('/api/documents/upload', ['file' => $file]);
        $response->assertStatus(200)->assertJson(['message' => 'Berhasil upload']);
    }

    /** @test */
    public function pengguna_bisa_melihat_daftar_dokumen_miliknya()
    {
        // PERBAIKAN: Gunakan Sequence agar setiap dokumen punya access_token unik
        Document::factory()
            ->count(2)
            ->sequence(
                ['access_token' => Str::uuid()],
                ['access_token' => Str::uuid()]
            )
            ->create([
                'user_id' => $this->user->id,
                'encrypted_original_filename' => Crypt::encryptString('doc.pdf|'.Str::uuid()),
                'status' => 'pending' 
            ]);
        
        $response = $this->getJson('/api/documents/user'); 
        $response->assertStatus(200)
            ->assertJson(['status' => true])
            ->assertJsonCount(2, 'documents');
    }

    /** @test */
    public function pengguna_bisa_membatalkan_dokumen_yang_belum_ditandatangani()
    {
        $token = Str::uuid()->toString();
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'access_token' => $token,
            'status' => 'pending'
        ]);

        $response = $this->deleteJson("/api/documents/cancel/{$token}");
        $response->assertStatus(200);
        
        // Pastikan status berubah jadi cancelled
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'status' => 'cancelled'
        ]);

        // Cek Soft Delete (deleted_at tidak null)
        // Jika Controller Anda TIDAK melakukan $document->delete(), hapus baris ini
        $this->assertSoftDeleted('documents', ['id' => $document->id]);
    }

    /** @test */
    public function pengguna_tidak_bisa_membatalkan_dokumen_yang_sudah_ditandatangani()
    {
        $token = Str::uuid()->toString();
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'access_token' => $token,
            'verified_at' => now(),
            'status' => 'verified'
        ]);
        
        Signature::factory()->create([
            'document_id' => $document->id,
            'status' => 'approved'
        ]);

        $response = $this->deleteJson("/api/documents/cancel/{$token}");
        $response->assertStatus(403);
    }
}