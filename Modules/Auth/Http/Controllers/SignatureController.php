<?php
namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Auth\Entities\Document;
use Modules\Auth\Entities\Signature;
use App\Models\Core\User;

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

    $token = Str::uuid()->toString();

    $signature = Signature::create([
        'document_id' => $documentId,
        'signer_id' => $signerId,
        'status' => 'pending',
        'sign_token' => $signToken,
    ]);
   $encryptedLink = Crypt::encryptString(
        $document->user->name . '::' . $document->encrypted_original_filename . '::' . $signToken
    );

    return response()->json([
        'message' => 'Penandatangan berhasil ditambahkan',
        'sign_token' => $signToken,
        'signer_id' => $signerId,
        'encrypted_link' => $encryptedLink,
    ]);
}
   public function viewFromPayload(Request $request)
{
    try {
        $payload = Crypt::decryptString($request->payload);
        [$creatorName, $encryptedFilename, $signToken] = explode('::', $payload);

        $document = Document::where('encrypted_original_filename', $encryptedFilename)->firstOrFail();

        $signature = $document->signatures()
            ->where('sign_token', $signToken)
            ->first();

        if (!$signature) {
            return response()->json(['message' => 'Token tanda tangan tidak valid'], 403);
        }

        return response()->json([
            'creator' => $creatorName,
            'original_name' => Crypt::decryptString($document->encrypted_original_filename),
            'sign_token' => $signToken,
            'tujuan' => $document->tujuan,
            'signer_id' => $signature->signer_id,
            'access_token' => $document->access_token,
        ]);

    } catch (\Exception $e) {
        return response()->json(['message' => 'Payload tidak valid'], 400);
    }
}}