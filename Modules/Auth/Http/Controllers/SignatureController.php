<?php
namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Auth\Entities\Document;
use Modules\Auth\Entities\Signature;
use App\Models\Core\User;
use Illuminate\Support\Facades\Crypt;

class SignatureController extends Controller
{
    public function addSigner($documentId, Request $request)
    {
    $document = Document::findOrFail($documentId);

    if (empty($document->tujuan)) {
        $request->validate([
            'nip' => 'required|exists:users,nip',
            'alasan' => 'required|string|max:255',
        ]);

        $document->tujuan = $request->alasan;
        $document->save();
    } else {
        $request->validate([
            'nip' => 'required|exists:users,nip',
        ]);
    }
    $targetUser = User::where('nip', $request->nip)->firstOrFail();
    $signerId = $targetUser->id;

    $exists = Signature::where('document_id', $documentId)
        ->where('signer_id', $signerId)
        ->exists();

    if ($exists) {
        return response()->json(['message' => 'Penandatangan sudah ditambahkan'], 409);
    }

    $signToken = Str::uuid()->toString();

    $signature = Signature::create([
        'document_id' => $documentId,
        'signer_id' => $signerId,
        'status' => 'pending',
        'sign_token' => $signToken,
    ]);
 
    return response()->json([
        'message' => 'Penandatangan berhasil ditambahkan',
        'sign_token' => $signToken,
        'signer_id' => $signerId,
    ]);
    }
    public function viewFromPayload($signToken)
    {
    try {
        $signature = Signature::where('sign_token', $signToken)->firstOrFail();
        $document = $signature->document;

        $allSignatures = $document->signatures->map(function ($sig) {
            return [
                'sign_token' => $sig->sign_token,
                'signer_id' => $sig->signer_id,
                'status' => $sig->status,
                'name' => optional($sig->user)->name,
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
            'message' => 'Payload tidak valid atau UUID tidak ditemukAn',
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
    $signatures = $user->signatures()->get();
    $result = $signatures->map(function ($signature) {
        $document = $signature->document;
        $decrypted = Crypt::decryptString($document->encrypted_original_filename);
        [$originalName, $token] = explode('|', $decrypted);
        return [
            'document_id' => $document->id,
            'sign_token' => $signature->sign_token,
            'original_name' => $originalName,
            'uploaded_at' => $document->created_at->toDateTimeString(),
            'tujuan' => $document->tujuan,
            'review_url' => url("/api/documents/review/{$document->access_token}"),
            'access_token' => $document->access_token,
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

    $signatures = $user->signatures()->where('status', 'pending')->with('document')->get();

    $result = $signatures->map(function ($signature) {
        $document = $signature->document;
        try {
            $decrypted = Crypt::decryptString($document->encrypted_original_filename);
            [$originalName, $token] = explode('|', $decrypted);
        } catch (\Exception $e) {
            $originalName = 'Tidak bisa didekripsi';
        }

        return [
            'document_id' => $document->id,
            'sign_token' => $signature->sign_token,
            'original_name' => $originalName,
            'uploaded_at' => $document->created_at->toDateTimeString(),
            'tujuan' => $document->tujuan,
            'review_url' => url("/api/documents/review/{$document->access_token}"),
            'access_token' => $document->access_token,
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
    ]);

    try {
        $signature = Signature::where('sign_token', $signToken)->firstOrFail();

        if ($signature->status !== 'pending') {
            return response()->json(['message' => 'Tanda tangan sudah diproses sebelumnya'], 409);
        }

        $signature->status = $request->status;
        $signature->save();
        $document = $signature->document;
        if ($request->status === 'approved') {
            $allApproved = $document->signatures()
                ->where('status', '!=', 'approved')
                ->count() === 0;

            if ($allApproved) {
                $document->verified_at = now();
                $document->save();
            }

            return response()->json([
                'message' => 'Tanda tangan berhasil disetujui',
                'document_verified' => $allApproved,
            ]);

        } else if ($request->status === 'rejected') {
            $document->verified_at = null; 
            $document->save();

            return response()->json([
                'message' => 'Tanda tangan berhasil ditolak',
                'document_verified' => false,
            ]);
        }

    } catch (\Exception $e) {
        return response()->json(['message' => 'Token tidak valid atau tanda tangan tidak ditemukan'], 400);
    }
    }
}