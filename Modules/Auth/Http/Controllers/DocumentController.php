<?php
namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Modules\Auth\Entities\Document;
use Illuminate\Validation\ValidationException;
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
        $encryptedName = Crypt::encryptString($originalName);

        $randomName = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents', $randomName, 'private');

        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $token = Str::uuid()->toString();
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
            'file_url' => asset('storage/' . $path),
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


   public function downloadWithSignToken(Request $request)
{
    $request->validate([
        'access_token' => 'required|string',
        'sign_token' => 'required|string',
    ]);

    $document = Document::where('access_token', $request->access_token)->first();

    if (!$document) {
        return response()->json(['message' => 'Dokumen tidak ditemukan'], 404);
    }

    $signature = $document->signatures()
        ->where('sign_token', $request->sign_token)
        ->first();

    if (!$signature) {
        return response()->json(['message' => 'Token tanda tangan tidak valid'], 403);
    }

    return \Storage::disk('private')->download(
        $document->file_path,
        Crypt::decryptString($document->encrypted_original_filename)
    );
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
            return [
                'id' => $doc->id,
                'file_url' => asset('storage/' . $doc->file_path),
                'uploaded_at' => $doc->created_at->toDateTimeString(),
                'encrypted_name' => $doc->encrypted_original_filename,
            ];
        });

    return response()->json([
        'status' => true,
        'documents' => $documents,
    ]);
}
 public function cancelRequest($id)
{
    $user = auth()->user();
    $document = Document::where('id', $id)->where('user_id', $user->id)->first();
    if (!$document) {
        return response()->json(['message' => 'Dokumen tidak ditemukan'], 404);
    }
    $hasSigned = $document->signatures()->where('status', 'signed')->exists();
    if ($hasSigned) {
        return response()->json(['message' => 'Tidak bisa dihapus, sudah ada tanda tangan'], 403);
    }
    $document->signatures()->delete();
    if (\Storage::disk('private')->exists($document->file_path)) {
        \Storage::disk('private')->delete($document->file_path);
    }
    $document->delete();
    return response()->json(['message' => 'Permintaan tanda tangan dibatalkan dan dokumen dihapus']);
}
public function replacePdfWithQr(Request $request, $documentId)
{
    $request->validate([
        'new_file' => 'required|file|mimes:pdf',
        'access_token' => 'required|string',
    ]);
    $user = auth()->user();
    $document = Document::where('id', $documentId)
        ->where('access_token', $request->access_token)
        ->first();
    if (!$document) {
        return response()->json(['message' => 'Dokumen tidak ditemukan atau token tidak valid'], 404);
    }
    if ($document->user_id !== $user->id) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    if (Storage::disk('private')->exists($document->file_path)) {
        Storage::disk('private')->delete($document->file_path);
    }

    $newFile = $request->file('new_file');
    $newPath = $newFile->storeAs('documents', basename($document->file_path), 'private');

    $document->update([
        'file_path' => $newPath,
    ]);

    return response()->json([
        'message' => 'PDF berhasil diganti',
        'document_id' => $document->id,
    ]);
}

}
   



