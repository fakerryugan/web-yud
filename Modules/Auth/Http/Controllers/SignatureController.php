<?php
namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Auth\Entities\Document;
use Modules\Auth\Entities\Signature;
use App\Models\Core\User; // <-- Saya asumsikan path ini benar untuk Anda
use Illuminate\Support\Facades\Crypt;

// <-- PERBAIKAN: Tambahkan 'use' statement yang hilang untuk FCM & Log -->
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;
use Kreait\Laravel\Firebase\Facades\Firebase;
// <-- Akhir Perbaikan -->

class SignatureController extends Controller
{
 public function addSigner($accessToken, Request $request)
{
    // 1. Cari Dokumen
    $document = Document::where('access_token', $accessToken)->first();

    if (!$document) {
        return response()->json(['message' => 'Dokumen tidak ditemukan'], 404);
    }

    $request->validate([
        'nip' => 'required|exists:users,nip',
        'tujuan' => 'nullable|string|max:255',
    ]);

    // 2. Cari User Target
    $targetUser = User::where('nip', $request->nip)->firstOrFail();
    
    // 3. Cek apakah sudah ada (Wajib pakai 'signer_id')
    $exists = Signature::where('document_id', $document->id)
        ->where('signer_id', $targetUser->id) // <--- SESUAI DATABASE ANDA
        ->exists();

    if ($exists) {
        return response()->json(['message' => 'User ini sudah menjadi penandatangan'], 409);
    }

    // 4. Update Tujuan (Jika perlu)
    if (empty($document->tujuan) && $request->filled('tujuan')) {
        $document->tujuan = $request->tujuan;
        $document->save();
    }

    // 5. Buat Token UUID (36 Karakter sesuai kolom char(36))
    $signToken = Str::uuid()->toString();

    // 6. Simpan ke Database
    try {
        $signature = Signature::create([
            'document_id' => $document->id,
            'signer_id'   => $targetUser->id, // <--- PENTING: JANGAN 'user_id'
            'status'      => 'pending',       // Sesuai default, tapi kita set eksplisit
            'sign_token'  => $signToken,      // Masuk ke kolom char(36)
            // 'comment', 'signed_at' biarkan NULL (sesuai settingan DB "Ya NULL")
        ]);

        // --- (LOGIKA FCM DISINI - Copy dari kode sebelumnya) ---

        return response()->json([
            'success' => true,
            'message' => 'Penandatangan berhasil ditambahkan',
            'sign_token' => $signToken,
            'signer_id' => $targetUser->id,
        ]);

    } catch (\Illuminate\Database\QueryException $e) {
        // Ini akan menangkap jika ada error SQL (misal salah nama kolom)
        \Log::error("SQL Error: " . $e->getMessage());
        return response()->json(['message' => 'Database Error: ' . $e->getMessage()], 500);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Server Error: ' . $e->getMessage()], 500);
    }
}
    
    public function viewFromPayload($signToken)
    {
        try {
            // <-- PERBAIKAN: Perbaiki N+1 Query dengan Eager Loading -->
            $signature = Signature::where('sign_token', $signToken)
                ->with('document.signatures.user') // Load semua relasi dalam 1 query
                ->firstOrFail();
            // <-- Akhir Perbaikan -->

            $document = $signature->document;

            $allSignatures = $document->signatures->map(function ($sig) {
                return [
                    'sign_token' => $sig->sign_token,
                    'signer_id' => $sig->signer_id,
                    'status' => $sig->status,
                    'name' => optional($sig->user)->name, // Tidak ada query N+1 lagi
                ];
            });

            return response()->json([
                'document_id' => $document->id,
                'original_name' => explode('|', Crypt::decryptString($document->encrypted_original_filename))[0],
                'access_token' => $document->access_token,
                'tujuan' => $document->tujuan,
                'current_sign_token' => $signToken,
                'current_signer_id' => $signature->signer_id,
                'current_status' => $signature->status,
                'download_url' => url("/api/documents/download/{$document->access_token}/{$document->encrypted_original_filename}"),
                'signers' => $allSignatures,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Payload tidak valid atau UUID tidak ditemukan',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    public function listSign()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        // <-- PERBAIKAN: Perbaiki N+1 Query dengan Eager Loading -->
        $signatures = $user->signatures()->with('document')->get();
        // <-- Akhir Perbaikan -->
        
        $result = $signatures->map(function ($signature) {
            $document = $signature->document; // Tidak ada query N+1 lagi
            
            // <-- PERBAIKAN: Tambahkan try-catch untuk dekripsi -->
            try {
                $decrypted = Crypt::decryptString($document->encrypted_original_filename);
                [$originalName, $token] = explode('|', $decrypted);
            } catch (\Exception $e) {
                $originalName = 'Nama file tidak valid';
            }
            // <-- Akhir Perbaikan -->

            return [
                'document_id' => $document->id,
                'sign_token' => $signature->sign_token,
                'original_name' => $originalName,
                'uploaded_at' => $document->created_at->toDateTimeString(),
                'tujuan' => $document->tujuan,
                'review_url' => url("/api/documents/review/{$document->access_token}"),
                'access_token' => $document->access_token,
                'status' => $document->status, // <-- PERBAIKAN: Kirim status dokumen
            ];
        });

        return response()->json([
            'status' => true,
            'documents' => $result,
        ]);
    }

  public function listSignRequests()
{
    $user = auth()->user();

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $signatures = $user->signatures()
        ->where('status', 'pending')
        ->whereHas('document')
        // PERBAIKAN 1: Eager Load bertingkat (Document -> Signatures -> User)
        // Ini penting agar saat kita loop recipients, tidak terjadi query berulang (N+1)
        ->with(['document.signatures.user']) 
        ->get();

    $result = $signatures->map(function ($signature) {
        $document = $signature->document; 

        // Dekripsi nama file
        try {
            $decrypted = Crypt::decryptString($document->encrypted_original_filename);
            [$originalName, $token] = explode('|', $decrypted);
        } catch (\Exception $e) {
            $originalName = 'Tidak bisa didekripsi';
        }

        // PERBAIKAN 2: Logic Recipients dibuat di sini (sebelum return)
        // Menggunakan variabel $document (bukan $doc)
        $recipients = $document->signatures->map(function ($sig) {
            return [
                'nama' => optional($sig->user)->name ?? 'User Tidak Ditemukan',
                'status' => $sig->status,     // pending, approved, rejected
                'keterangan' => $sig->comment, // komentar jika ada
            ];
        });

        return [
            'document_id' => $document->id,
            'sign_token' => $signature->sign_token,
            'original_name' => $originalName,
            'uploaded_at' => $document->created_at->toDateTimeString(),
            'tujuan' => $document->tujuan,
            'review_url' => url("/api/documents/review/{$document->access_token}"),
            'access_token' => $document->access_token,
            // PERBAIKAN 3: Masukkan hasil mapping recipients ke sini
            'recipients' => $recipients, 
        ];
    });

    return response()->json([
        'status' => true,
        'documents' => $result,
    ]);
}
  public function processSignature(Request $request, $signToken)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'comment' => 'required_if:status,rejected|nullable|string|max:500',
        ], [
            'comment.required_if' => 'Komentar wajib diisi jika tanda tangan ditolak.',
        ]);

        try {
            // 1. Cari Signature berdasarkan Token & Load Dokumen
            $signature = Signature::where('sign_token', $signToken)
                                    ->with('document') 
                                    ->firstOrFail();

            $document = $signature->document;

            // 2. Validasi Status Dokumen (Cegah proses jika sudah final/batal)
            if (in_array($document->status, ['verified', 'rejected', 'cancelled'])) {
                return response()->json([
                    'message' => 'Dokumen ini sudah selesai (diverifikasi, ditolak, atau dibatalkan) dan tidak dapat diproses lagi.'
                ], 409);
            }

            // 3. Validasi Status Tanda Tangan
            if ($signature->status !== 'pending') {
                return response()->json(['message' => 'Tanda tangan sudah diproses sebelumnya'], 409);
            }

            // 4. Update Data Signature
            $signature->status = $request->status;
            $signature->comment = $request->comment ?? null;
            $signature->signed_at = now();
            $signature->save();

            // Ambil Pemilik Dokumen untuk dikirim Notifikasi
            $owner = $document->owner; 

            // --- SKENARIO A: APPROVED (DISETUJUI) ---
            if ($request->status === 'approved') {
                // Cek apakah SISA tanda tangan yang belum approved = 0
                $allApproved = $document->signatures()
                    ->where('status', '!=', 'approved')
                    ->count() === 0;

                if ($allApproved) {
                    // Jika semua sudah setuju, update dokumen jadi Verified
                    $document->verified_at = now();
                    $document->status = 'verified';
                    $document->save();

                    // KIRIM NOTIFIKASI KE PEMILIK (DOKUMEN SELESAI)
                    if ($owner && $owner->fcm_token) {
                        try {
                            $message = CloudMessage::withTarget('token', $owner->fcm_token)
                                ->withNotification(FcmNotification::create(
                                    'Dokumen Selesai Diverifikasi',
                                    'Dokumen "' . $document->original_name . '" telah disetujui oleh semua pihak.'
                                ))
                                ->withData([
                                    'target_screen' => 'detail_document', 
                                    'access_token' => $document->access_token
                                ]);
                            
                            Firebase::messaging()->send($message);
                        } catch (\Throwable $e) {
                            Log::error("FCM Error (Verified): " . $e->getMessage());
                        }
                    }
                }

                return response()->json([
                    'message' => 'Dokumen berhasil disetujui.',
                    'document_verified' => $allApproved,
                ]);
            }

            // --- SKENARIO B: REJECTED (DITOLAK) ---
            if ($request->status === 'rejected') {
                // Jika satu orang menolak, dokumen langsung Rejected
                $document->verified_at = null;
                $document->status = 'rejected';
                $document->save();

                // KIRIM NOTIFIKASI KE PEMILIK (DOKUMEN DITOLAK)
                if ($owner && $owner->fcm_token) {
                    try {
                        $signerName = auth()->user()->name;
                        $message = CloudMessage::withTarget('token', $owner->fcm_token)
                            ->withNotification(FcmNotification::create(
                                'Dokumen Ditolak',
                                "$signerName menolak dokumen \"{$document->original_name}\". Alasan: {$signature->comment}"
                            ))
                            ->withData([
                                'target_screen' => 'detail_document', 
                                'access_token' => $document->access_token
                            ]);
                        
                        Firebase::messaging()->send($message);
                    } catch (\Throwable $e) {
                        Log::error("FCM Error (Rejected): " . $e->getMessage());
                    }
                }

                return response()->json([
                    'message' => 'Dokumen ditolak dengan komentar.',
                    'comment' => $signature->comment,
                    'document_verified' => false,
                ]);
            }

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Token tidak valid atau tanda tangan tidak ditemukan'], 404);
        } catch (\Exception $e) {
            Log::error('Gagal process signature: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan pada server'], 500);
        }
    }
  public function listCancellationRequests()
{
    $user = auth()->user();

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $signatures = $user->signatures()
        ->where('status', 'approved') 
        ->whereHas('document', function ($query) {
            $query->where('status', 'cancellation_requested');
        })
        ->with(['document.owner']) // Load owner untuk tahu siapa yang minta batal
        ->get();

    $result = $signatures->map(function ($signature) {
        $document = $signature->document;
        try {
            $decrypted = Crypt::decryptString($document->encrypted_original_filename);
            [$originalName,] = explode('|', $decrypted);
        } catch (\Exception $e) {
            $originalName = 'Tidak bisa didekripsi';
        }

        return [
            'document_id' => $document->id,
            'sign_token' => $signature->sign_token, 
            'original_name' => $originalName,
            'tujuan' => $document->tujuan,
            'status' => $document->status,
            
            // --- DATA BARU UNTUK DITAMPILKAN ---
            'diminta_batal_oleh' => optional($document->owner)->name ?? 'Pemilik Dokumen',
            'alasan_pembatalan' => $document->cancellation_reason, // <--- INI ALASANNYA
            'tanggal_permintaan' => $document->updated_at->format('d M Y H:i'),
        ];
    });

    return response()->json([
        'status' => true,
        'documents' => $result,
    ]);
}
public function approveCancellation(Request $request, $signToken)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            // 1. Ambil signature user saat ini
            $signature = Signature::where('sign_token', $signToken)
                ->where('signer_id', $user->id)
                ->firstOrFail();

            $document = $signature->document;

            // Validasi Status Dokumen (Harus sedang requested)
            if ($document->status !== 'cancellation_requested') {
                return response()->json(['message' => 'Dokumen ini tidak sedang dalam proses permintaan pembatalan.'], 400);
            }
            
            // Validasi Status Signature (Harus approved untuk bisa membatalkan)
            if ($signature->status !== 'approved') {
                if ($signature->status === 'cancellation_approved') {
                     return response()->json(['message' => 'Anda sudah menyetujui pembatalan sebelumnya.']);
                }
                return response()->json(['message' => 'Status tanda tangan Anda bukan "approved".'], 400);
            }

            // 2. Update Status Signature User Ini
            $signature->status = 'cancellation_approved';
            $signature->save();

            // 3. Cek Sisa Penandatangan (Database Query)
            // Hitung user lain yang statusnya MASIH 'approved' (artinya belum setuju batal)
            $remainingApprovals = $document->signatures()
                ->where('status', 'approved') 
                ->count();

            // 4. Jika SISANYA 0, berarti SEMUA SUDAH setuju batal
            if ($remainingApprovals === 0) {
                
                // Update status dokumen jadi cancelled
                $document->status = 'cancelled';
                $document->save();
                
                // Soft Delete Dokumen
                $document->delete(); 
                
                Log::info("Dokumen ID $document->id telah dibatalkan sepenuhnya.");

                // KIRIM NOTIFIKASI KE PEMILIK (DOKUMEN BERHASIL DIHAPUS)
                $owner = $document->owner;
                if ($owner && $owner->fcm_token) {
                    try {
                        $message = CloudMessage::withTarget('token', $owner->fcm_token)
                            ->withNotification(FcmNotification::create(
                                'Dokumen Berhasil Dihapus',
                                'Semua pihak telah menyetujui pembatalan dokumen "' . $document->original_name . '".'
                            ))
                            ->withData(['target_screen' => 'history']); // Arahkan ke history atau dashboard
                        
                        Firebase::messaging()->send($message);
                        Log::info("FCM Pembatalan Sukses dikirim ke owner ID: " . $owner->id);

                    } catch (\Throwable $e) {
                        Log::error("FCM Error (Cancellation Approved): " . $e->getMessage());
                    }
                }

                return response()->json(['message' => 'Pembatalan disetujui oleh semua pihak. Dokumen telah dihapus.']);
            }

            // Jika masih ada yang belum setuju
            return response()->json(['message' => 'Anda menyetujui pembatalan. Menunggu persetujuan pihak lain.']);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            Log::error('Gagal approve cancellation: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal.'], 500);
        }
    }
}