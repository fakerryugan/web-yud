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

class DocumentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    // Fungsi ini dijalankan sebelum setiap tes di file ini
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private'); // Gunakan 'disk' palsu agar tidak mengotori storage asli
        
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function pengguna_bisa_mengunggah_dokumen()
    {
        $file = UploadedFile::fake()->create('dokumen_tes.pdf', 100);

        $response = $this->postJson('/api/documents/upload', ['file' => $file]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Berhasil upload']);

        // Pastikan data dokumen tersimpan di database
        $this->assertDatabaseHas('documents', ['user_id' => $this->user->id]);
        
        // Pastikan file fisik tersimpan di storage
        $document = Document::first();
        Storage::disk('private')->assertExists($document->file_path);
    }

    /** @test */
    public function pengguna_bisa_melihat_daftar_dokumen_miliknya()
    {
        // Buat 2 dokumen untuk pengguna yang sedang login
        Document::factory()->count(2)->create(['user_id' => $this->user->id]);
        // Buat 1 dokumen untuk pengguna lain (sebagai pengecoh)
        Document::factory()->create();

        $response = $this->getJson('/api/documents/user');

        $response->assertStatus(200)
            ->assertJson(['status' => true])
            // Pastikan hanya 2 dokumen yang ditampilkan
            ->assertJsonCount(2, 'documents');
    }

    /** @test */
    public function pengguna_bisa_membatalkan_dokumen_yang_belum_ditandatangani()
    {
        $document = Document::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/documents/cancel/{$document->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Permintaan tanda tangan dibatalkan dan dokumen dihapus']);
        
        // Pastikan data hilang dari DB dan file hilang dari storage
        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
        Storage::disk('private')->assertMissing($document->file_path);
    }

    /** @test */
    public function pengguna_tidak_bisa_membatalkan_dokumen_yang_sudah_ditandatangani()
    {
        $document = Document::factory()->create(['user_id' => $this->user->id]);
        Signature::factory()->create([
            'document_id' => $document->id,
            'status' => 'signed', // atau 'approved' sesuai logika Anda
        ]);

        $response = $this->deleteJson("/api/documents/cancel/{$document->id}");

        // Pengecekan: Ditolak dengan status 403 Forbidden
        $response->assertStatus(403)
            ->assertJson(['message' => 'Tidak bisa dihapus, sudah ada tanda tangan']);
    }
}