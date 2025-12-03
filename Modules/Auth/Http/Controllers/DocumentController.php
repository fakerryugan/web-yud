<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Auth\Notifications\DocumentReceivedNotification;
use Modules\Auth\Entities\Document;
use Modules\Auth\Entities\Signature;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;
use Kreait\Laravel\Firebase\Facades\Firebase;

// 3 'use' statement ini sudah benar dari sebelumnya
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class DocumentController extends Controller
{
    public function upload(Request $request)
    {
       try {
            $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx',
            ]);

            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();

            $user = auth()->user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            $token = Str::uuid()->toString();
            $nameWithToken = $originalName . '|' . $token;
            $encryptedName = Crypt::encryptString($nameWithToken);
            $fileNameBase = sha1($nameWithToken);
            $fileName = $fileNameBase . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $fileName, 'private');
            $document = Document::create([
                'user_id' => $user->id,
                'file_path' => $path,
                'encrypted_original_filename' => $encryptedName,
                'tujuan' => null,
                'access_token' => $token,
            ]);

            return response()->json([
                'message' => 'Berhasil upload',
                'access_token' => $token,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Upload error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Upload gagal',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function download($accessToken, $encryptedName)
    {
        try {
            $document = Document::where('access_token', $accessToken)
                ->where('encrypted_original_filename', $encryptedName)
                ->firstOrFail();

            $filePath = storage_path('app/private/' . $document->file_path);

            if (!file_exists($filePath)) {
                return response()->json(['message' => 'File tidak ditemukan'], 404);
            }

            $decrypted = Crypt::decryptString($document->encrypted_original_filename);
            [$originalName, $token] = explode('|', $decrypted);

            return response()->download($filePath, $originalName);
        } catch (\Exception $e) {
            \Log::error('Download error: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mendownload file'], 400);
        }
    }
    public function review($accessToken)
    {
        try {
            $document = Document::where('access_token', $accessToken)->firstOrFail();

            $filePath = storage_path('app/private/' . $document->file_path);

            if (!file_exists($filePath)) {
                return response()->json(['message' => 'File tidak ditemukan'], 404);
            }

            // <-- PERBAIKAN: Hapus header Content-Type hardcode 'application/pdf'
            // Biarkan Laravel yang mendeteksi MIME type file secara otomatis.
            return response()->file($filePath);

        } catch (\Exception $e) {
            \Log::error('Review error: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal menampilkan file'], 400);
        }
    }


    public function listUserDocuments(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan',
            ], 401);
        }

        $documents = Document::where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($doc) {
                try {
                    $decrypted = Crypt::decryptString($doc->encrypted_original_filename);
                    $parts = explode('|', $decrypted);
                    $originalName = $parts[0];
                } catch (\Exception $e) {
                    $originalName = 'Dokumen tidak dapat dibaca';
                }

                return [
                    'id' => $doc->id,
                    'original_name' => $originalName,
                    'uploaded_at' => $doc->created_at->toDateTimeString(),
                    'access_token' => $doc->access_token,
                    'tujuan' => $doc->tujuan,
                    'status' => $doc->status, // <-- PERBAIKAN: Kirim status agar Flutter tahu
                ];
            });

        return response()->json([
            'status' => true,
            'documents' => $documents,
        ]);
    }

   public function listCompletedDocuments(Request $request)

    {

       $user = auth()->user();



        if (!$user) {

            return response()->json([

                'status' => false,

                'message' => 'User tidak ditemukan',

            ], 401);

        }



        // Ambil dokumen dan relasinya (signatures DAN user dari signature)

        $documents = Document::where('user_id', $user->id)

            // PENTING: Eager load 'user' yang terkait 'signatures'

            ->with('signatures.user') 

            ->latest()

            ->get()

            ->map(function ($doc) {

                try {

                    $decrypted = Crypt::decryptString($doc->encrypted_original_filename);

                    $parts = explode('|', $decrypted);

                    $originalName = $parts[0];

                } catch (\Exception $e) {

                    \Log::error('Gagal dekripsi nama file untuk dokumen ID: ' . $doc->id);

                    $originalName = 'Nama file tidak valid';

                }



                // --- Logika Status (Sudah Benar) ---

                $status = 'Pending'; 

                if ($doc->verified_at) {

                    $status = 'Diverifikasi';

                } else {

                    $isRejected = $doc->signatures->where('status', 'rejected')->isNotEmpty();

                    if ($isRejected) {

                        $status = 'Ditolak';

                    }

                }

                // --- Akhir Logika Status ---





                // --- INI BAGIAN PENTINGNYA ---

                // Ubah koleksi $doc->signatures yang sudah di-load menjadi array

                // yang siap dikonsumsi oleh Flutter

                $recipients = $doc->signatures->map(function ($sig) {

                    return [

                        // 'nama' diambil dari relasi 'user' yang sudah di-load

                        'nama' => optional($sig->user)->name ?? 'User Tidak Ditemukan',

                        'status' => $sig->status, // (pending, approved, rejected)

                        

                        // INILAH YANG ANDA MINTA:

                        'keterangan' => $sig->comment, // (null atau berisi komentar)

                    ];

                });

                // --- AKHIR BAGIAN PENTING ---



                return [

                    'id' => $doc->id,

                    'original_name' => $originalName,

                    'status' => $status, // Status keseluruhan dokumen

                    'uploaded_at' => $doc->created_at->toDateTimeString(),

                    'encrypted_original_filename' => $doc->encrypted_original_filename,

                    'access_token' => $doc->access_token,

                    

                    // Sesuaikan key untuk Flutter

                    'tujuan_surat' => $doc->tujuan,

                    

                    // Data baru yang dikirim ke Flutter

                    'recipients' => $recipients, 

                ];

            });



        return response()->json([

            'status' => true,

            'documents' => $documents,

        ]);

    }
    
   public function replacePdfQr(Request $request, $accessToken) // Ubah parameter jadi accessToken
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf',
        ]);

        // Ubah pencarian berdasarkan access_token
        $document = Document::where('access_token', $accessToken)->firstOrFail();

        if ($request->hasFile('pdf')) {
            if ($document->file_path && Storage::disk('private')->exists($document->file_path)) {
                Storage::disk('private')->delete($document->file_path);
            }

            $file = $request->file('pdf');
            $fileName = basename($document->file_path);
            $newPath = $file->storeAs('documents', $fileName, 'private');

            $document->file_path = $newPath;
            $document->save();
        }

        // Gunakan $document->id (dari hasil query di atas) karena $documentId sudah tidak ada di parameter
        $signatures = Signature::where('document_id', $document->id)->with('user')->get();

        try {
            $decrypted = Crypt::decryptString($document->encrypted_original_filename);
            [$originalDocName,] = explode('|', $decrypted);
        } catch (\Exception $e) {
            $originalDocName = 'dokumen';
        }

        foreach ($signatures as $signature) {
            $userToNotify = $signature->user;

            if ($userToNotify && $userToNotify->fcm_token) {
                $message = CloudMessage::withTarget('token', $userToNotify->fcm_token)
                    ->withNotification(FcmNotification::create(
                        'Dokumen Siap Ditandatangani',
                        'Dokumen "' . $originalDocName . '" siap ditandatangani.'
                    ))
                    ->withData([
                        'target_screen' => 'verification',
                        'document_id' => (string) $document->id,
                        'sign_token' => $signature->token,
                        'access_token' => $document->access_token,
                    ]);

                try {
                    Firebase::messaging()->send($message);
                } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
                    \Log::warning('❌ Token FCM tidak valid untuk user ID ' . $userToNotify->id . ': ' . $userToNotify->fcm_token);
                    continue; 
                } catch (\Throwable $e) {
                    \Log::error('⚠️ Gagal kirim FCM ke user ID ' . $userToNotify->id . ': ' . $e->getMessage());
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil diperbarui dan notifikasi telah dikirim.',
            'document_id' => $document->id,
        ]);
    }
    
   // Tambahkan Request $request di parameter
  public function cancel(Request $request, $accessToken)
    {
        $user = auth()->user();

        try {
            // 1. Cari Dokumen milik user ini
            $document = Document::where('access_token', $accessToken)
                ->where('user_id', $user->id)
                ->with('signatures.user') // Eager load relasi
                ->firstOrFail();

            // 2. Cek jika sudah diverifikasi final, tidak bisa batal
            if ($document->verified_at !== null) {
                return response()->json(['message' => 'Dokumen tidak dapat dibatalkan, proses verifikasi sudah selesai.'], 403);
            }

            // 3. Filter siapa saja yang sudah Approve
            $approvedSigners = $document->signatures->where('status', 'approved');

            // -----------------------------------------------------------------
            // KASUS 1: BELUM ADA YANG MENYETUJUI (Hapus Langsung)
            // -----------------------------------------------------------------
            if ($approvedSigners->isEmpty()) {
                
                // Hapus file fisik (opsional, tergantung kebutuhan)
                /*
                if (Storage::disk('private')->exists($document->file_path)) {
                    Storage::disk('private')->delete($document->file_path);
                }
                */

                $document->delete(); // Soft Delete

                $document->status = 'cancelled';
                // Simpan alasan jika ada (sebagai history)
                if ($request->filled('reason')) {
                    $document->cancellation_reason = $request->reason;
                }
                $document->save();

                // Notifikasi log ke pending signers (Opsional, hanya log server)
                $pendingSigners = $document->signatures->where('status', 'pending');
                foreach ($pendingSigners as $signature) {
                    if ($signature->user) {
                        Log::info("Dokumen dihapus (belum ada yg ttd). Info ke user ID: " . $signature->user->id);
                    }
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Dokumen berhasil dibatalkan dan diarsipkan.'
                ], 200);
            }

            // -----------------------------------------------------------------
            // KASUS 2: SUDAH ADA YANG MENYETUJUI (Request Batal + Notif)
            // -----------------------------------------------------------------
            if ($approvedSigners->isNotEmpty()) {
                
                // Validasi Alasan Wajib
                $request->validate([
                    'reason' => 'required|string|max:500',
                ], [
                    'reason.required' => 'Alasan pembatalan wajib diisi karena dokumen sudah ditandatangani sebagian pihak.'
                ]);

                // Update Status Dokumen
                $document->status = 'cancellation_requested';
                $document->cancellation_reason = $request->reason;
                $document->save();
                
                // Loop ke semua user yang sudah Approve untuk kirim Notifikasi FCM
                foreach ($approvedSigners as $signature) {
                    $signerUser = $signature->user;
                    
                    if ($signerUser && $signerUser->fcm_token) {
                        try {
                            $message = CloudMessage::withTarget('token', $signerUser->fcm_token)
                                ->withNotification(FcmNotification::create(
                                    'Permintaan Pembatalan Dokumen',
                                    'Pemilik dokumen meminta pembatalan untuk "' . $document->original_name . '". Alasan: ' . $request->reason
                                ))
                                ->withData([
                                    'target_screen' => 'rejection_list', // Arahkan user ke halaman list penolakan/pembatalan
                                    'document_id' => (string) $document->id,
                                ]);
                            
                            Firebase::messaging()->send($message);
                            Log::info("FCM Pembatalan dikirim ke user ID: " . $signerUser->id);

                        } catch (\Throwable $e) {
                            Log::error("Gagal kirim FCM batal ke user {$signerUser->id}: " . $e->getMessage());
                        }
                    }
                }
                
                return response()->json([
                    'message' => 'Permintaan pembatalan dikirim. Menunggu persetujuan pihak lain.',
                    'action' => 'cancellation_request_sent',
                    'fileName' => $document->original_name,
                ], 202); 
            }

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Dokumen tidak ditemukan'], 404);
        } catch (\Exception $e) {
            Log::error("Gagal membatalkan dokumen Token $accessToken: " . $e->getMessage());
            return response()->json(['message' => 'Gagal membatalkan dokumen. ' . $e->getMessage()], 500);
        }
    }
    public function permanentDeleteDocument($accessToken) // Ubah parameter jadi accessToken
    {
        // Ubah pencarian find() menjadi where access_token
        $document = Document::withTrashed()->where('access_token', $accessToken)->first();

        if (!$document) {
            return response()->json(['message' => 'Dokumen tidak ditemukan'], 404);
        }

        DB::beginTransaction();
        try {
            $document->signatures()->delete();

            if (Storage::disk('private')->exists($document->file_path)) {
                Storage::disk('private')->delete($document->file_path);
            }

            $document->forceDelete();

            DB::commit();
            return response()->json(['message' => 'Dokumen telah dihapus secara permanen.'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Gagal hapus permanen dokumen Token $accessToken: " . $e->getMessage()); 
            return response()->json(['message' => 'Gagal menghapus dokumen.'], 500);
        }
    }
}