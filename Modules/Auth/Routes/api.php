<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\DocumentController;
use Modules\Auth\Http\Controllers\SignatureController;




// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/documents/upload', [DocumentController::class, 'upload']);
    Route::get('/documents/user', [DocumentController::class, 'listUserDocuments']);
    Route::delete('/documents/cancel/{id}', [DocumentController::class, 'cancelRequest']);
    Route::post('/documents/replace/{documentId}', [DocumentController::class, 'replacePdfWithQr']);
    Route::post('/documents/{documentId}/add-signer', [SignatureController::class, 'addSigner']);
});

Route::post('/documents/download', [DocumentController::class, 'downloadWithSignToken']);
Route::post('/signature/view-from-payload', [SignatureController::class, 'viewFromPayload']);

// Authenticated user info
Route::middleware('auth:sanctum')->get('/auth', function (Request $request) {
    return $request->user();
});
