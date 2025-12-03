<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\DocumentController;
use Modules\Auth\Http\Controllers\SignatureController;

// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/update-fcm-token', [AuthController::class, 'updateFcmToken'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/documents/upload', [DocumentController::class, 'upload']);
    Route::get('/documents/user', [DocumentController::class, 'listUserDocuments']);
    
    // UBAH {documentId} JADI {accessToken} agar cocok dengan Controller
    Route::delete('/documents/cancel/{accessToken}', [DocumentController::class, 'cancel']); 
    
    Route::post('/documents/replace/{documentId}', [DocumentController::class, 'replacePdfQr']);
    
    // Perhatikan URL ini untuk Test Signature
    Route::post('/documents/{accessToken}/signer', [SignatureController::class, 'addSigner']);
    
    Route::get('/signature/user', [SignatureController::class, 'listSignRequests']);
    Route::post('/documents/signature/{signToken}', [SignatureController::class, 'processSignature']);
    Route::post('/documents/review/{accessToken}', [DocumentController::class, 'review']);
    Route::get('/documents/completed', [DocumentController::class, 'listCompletedDocuments']);
    Route::get('/signatures/cancellation-requests', [SignatureController::class, 'listCancellationRequests']);
    Route::post('/signatures/approve-cancellation/{signToken}', [SignatureController::class, 'approveCancellation']);
});

Route::get('/view/{signToken}', [SignatureController::class, 'viewFromPayload']);
Route::get('/documents/download/{accessToken}/{encryptedName}', [DocumentController::class, 'download']);

Route::middleware('auth:sanctum')->get('/auth', function (Request $request) {
    return $request->user();
});