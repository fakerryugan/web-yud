<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Auth\Entities\Document;

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
                'document_id' => $document->id,
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

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
        ]);
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
            $decrypted = Crypt::decryptString($doc->encrypted_original_filename);
            [$originalName, $token] = explode('|', $decrypted);
            return [
                'id' => $doc->id,
                'original_name' => $originalName,
                'uploaded_at' => $doc->created_at->toDateTimeString(),
                'access_token' => $doc->access_token,
                'tujuan' => $doc->tujuan,
            ];
        });

    return response()->json([
        'status' => true,
        'documents' => $documents,
    ]);
    }


    public function cancel($documentId)
    {
        $user = auth()->user();
        $document = Document::where('id', $documentId)
            ->where('user_id', $user->id)
            ->first();

        if (!$document) {
            return response()->json(['message' => 'Dokumen tidak ditemukan'], 404);
        }

        $hasSigned = $document->signatures()->where('status', 'signed')->exists();
        if ($hasSigned) {
            return response()->json(['message' => 'Tidak bisa dihapus, sudah ada tanda tangan'], 403);
        }

        $document->signatures()->delete();

        if (Storage::disk('private')->exists($document->file_path)) {
            Storage::disk('private')->delete($document->file_path);
        }
        $document->delete();
        return response()->json(['message' => 'Permintaan tanda tangan dibatalkan dan dokumen dihapus']);
    }

    
    public function replacePdfQr(Request $request, $documentId)
    {
        $request->validate([
            'new_file' => 'required|file|mimes:pdf',
        ]);
        
        $user = auth()->user();
        $document = Document::where('id', $documentId)
            ->where('user_id', $user->id)
            ->first();

        if (!$document) {
            return response()->json(['message' => 'Dokumen tidak ditemukan atau Anda tidak berhak mengubahnya'], 404);
        }

        if (Storage::disk('private')->exists($document->file_path)) {
            Storage::disk('private')->delete($document->file_path);
        }

        $newFile = $request->file('new_file');
        $newPath = $newFile->storeAs('documents', basename($document->file_path), 'private');

        $document->update([
            'file_path' => $newPath,
        ]);

        // --- BAGIAN TAMBAHAN UNTUK NOTIFIKASI ---

        // 1. Temukan semua penerima/penandatangan dokumen ini dari database.
        //    Asumsinya, Anda punya model 'Signer' yang menyimpan user_id dan document_id.
        $recipients = Signer::where('document_id', $documentId)->with('user')->get();

        // 2. Loop setiap penerima dan kirim notifikasi.
        foreach ($recipients as $recipient) {
            $userToNotify = $recipient->user;

            // Pastikan user-nya ada dan memiliki fcm_token untuk dikirimi notifikasi.
            if ($userToNotify && $userToNotify->fcm_token) {
                $userToNotify->notify(new DocumentReceivedNotification($document));
            }
        }
        
        // --- AKHIR BAGIAN TAMBAHAN ---

        return response()->json([
            // Ubah pesan response agar lebih informatif
            'message' => 'PDF berhasil diganti dan notifikasi telah dikirim.',
            'document_id' => $document->id,
        ]);
    }

}
