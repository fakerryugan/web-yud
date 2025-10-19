<?php

namespace Modules\Auth\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Core\User;
use Laravel\Sanctum\Sanctum;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase; // Ini akan mereset database setelah setiap tes

    /** @test */
    public function pengguna_bisa_login_dengan_kredensial_valid()
    {
        // 1. Persiapan: Buat pengguna di database
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt('password123'),
        ]);

        // 2. Aksi: Kirim request login ke API
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        // 3. Pengecekan: Pastikan respons sukses (200) dan strukturnya benar
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'access_token',
                'token_type',
                'user' => ['id', 'name', 'username'],
            ])
            ->assertJson([
                'message' => 'Login berhasil',
            ]);
    }

    /** @test */
    public function pengguna_tidak_bisa_login_dengan_kredensial_invalid()
    {
        User::factory()->create(['username' => 'testuser']);

        // Aksi: Kirim request dengan password yang salah
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'password-salah',
        ]);

        // Pengecekan: Pastikan login gagal (401)
        $response->assertStatus(401)
            ->assertJson(['message' => 'Login gagal']);
    }

    /** @test */
    public function pengguna_terotentikasi_bisa_memperbarui_fcm_token()
    {
        // Persiapan: Buat pengguna dan "login" sebagai pengguna tersebut
        $user = User::factory()->create();
        Sanctum::actingAs($user); // Ini mensimulasikan pengguna yang sudah login
        $newFcmToken = 'ini-adalah-token-fcm-baru';

        // Aksi: Kirim request untuk update token
        $response = $this->postJson('/api/update-fcm-token', [
            'fcm_token' => $newFcmToken,
        ]);

        // Pengecekan: Pastikan respons sukses dan token di database terupdate
        $response->assertStatus(200)
            ->assertJson(['message' => 'FCM token berhasil diperbarui']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'fcm_token' => $newFcmToken,
        ]);
    }

    /** @test */
    public function pengguna_terotentikasi_bisa_logout()
    {
        // Persiapan: Login sebagai pengguna yang memiliki fcm_token
        $user = User::factory()->create(['fcm_token' => 'token-lama']);
        Sanctum::actingAs($user);

        // Aksi: Kirim request logout
        $response = $this->postJson('/api/logout');

        // Pengecekan: Pastikan sukses dan fcm_token di-reset menjadi null
        $response->assertStatus(200)
            ->assertJson(['message' => 'Logout berhasil']);
            
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'fcm_token' => null,
        ]);
    }
}