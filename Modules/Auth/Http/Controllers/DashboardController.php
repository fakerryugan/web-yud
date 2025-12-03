<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Modules\Auth\Entities\Document;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $keyword = $request->input('keyword');
        $statusFilter = $request->input('status');

        if ($user->role_aktif === 'pengurus' || $user->role_aktif === 'admin') {
            $documentsQuery = Document::with('signatures')->latest();
        } else {
            $documentsQuery = Document::where('user_id', $user->id)->with('signatures')->latest();
        }

        $allDocuments = $documentsQuery->get();

        $mapped = $allDocuments->map(function ($doc) {
            try {
                [$originalName, $token] = explode('|', Crypt::decryptString($doc->encrypted_original_filename));
            } catch (\Exception $e) {
                $originalName = 'Nama file tidak valid';
                $token = null;
            }

            $status = 'tertunda';
            if ($doc->signatures->isNotEmpty()) {
                // --- PERBAIKAN DI SINI ---
                // Mengganti fn($sig) => ... dengan function($sig) { return ...; }
                if ($doc->signatures->contains(function($sig) {
                    return $sig->status === 'rejected';
                })) {
                    $status = 'ditolak';
                // --- DAN DI SINI ---
                } elseif ($doc->signatures->contains(function($sig) {
                    return $sig->status === 'approved';
                })) {
                    $status = 'disetujui';
                }
            }

            return [
                'id' => $doc->id,
                'original_name' => $originalName,
                'short_name' => Str::limit($originalName, 40),
                'uploaded_at' => $doc->created_at->format('d-m-Y'),
                'uploaded_time' => $doc->created_at->format('H.i'),
                'status' => $status,
                'access_token' => $doc->access_token,
                'encrypted_name' => $doc->encrypted_original_filename,
            ];
        });

        if ($keyword) {
            // --- PERBAIKAN DI SINI ---
            // Menambahkan `use ($keyword)` agar variabel bisa diakses di dalam function
            $mapped = $mapped->filter(function($doc) use ($keyword) {
                return stripos($doc['original_name'], $keyword) !== false;
            });
        }

        if ($statusFilter) {
            // --- PERBAIKAN DI SINI ---
            // Menambahkan `use ($statusFilter)` agar variabel bisa diakses di dalam function
            $mapped = $mapped->filter(function($doc) use ($statusFilter) {
                return $doc['status'] === $statusFilter;
            });
        }

        $perPage = 10;
        $page = $request->get('page', 1);
        $documents = new LengthAwarePaginator(
            $mapped->forPage($page, $perPage),
            $mapped->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('dokumen.dashboard', compact('documents', 'statusFilter', 'keyword'));
    }

    public function download($accessToken, $encryptedName)
    {
        $document = Document::where('access_token', $accessToken)
            ->where('encrypted_original_filename', $encryptedName)
            ->firstOrFail();

        $filePath = storage_path('app/private/' . $document->file_path);
        if (!file_exists($filePath)) abort(404, 'File tidak ditemukan');

        [$originalName, $token] = explode('|', Crypt::decryptString($document->encrypted_original_filename));

        return response()->download($filePath, $originalName);
    }

    public function view($accessToken)
    {
        $document = Document::where('access_token', $accessToken)->firstOrFail();
        $filePath = storage_path('app/private/' . $document->file_path);

        if (!file_exists($filePath)) abort(404, 'File tidak ditemukan');

        return response()->file($filePath, ['Content-Type' => 'application/pdf']);
    }

      public function delete($documentId)
    {
        // Menghapus pengecekan role, asumsikan permission sudah di-handle sebelumnya
        $document = Document::where('id', $documentId)->firstOrFail();

        // Mengubah pengecekan 'signed' menjadi 'approved' atau 'rejected' 
        // agar konsisten dengan logika status di 'index'
        $hasSigned = $document->signatures()->whereIn('status', ['approved', 'rejected'])->exists();
        if ($hasSigned) return redirect()->back()->withErrors('Tidak bisa dihapus, dokumen sudah diproses (disetujui/ditolak)');

        $document->signatures()->delete();
        if (Storage::disk('private')->exists($document->file_path)) {
            Storage::disk('private')->delete($document->file_path);
        }
        $document->delete();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus');
    }
}