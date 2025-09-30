<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\DocumentController;
use Modules\Auth\Http\Controllers\SignatureController;
use App\Services\FcmService;
use GuzzleHttp\Client;

use App\Models\Core\User;

Route::get('/send-test-notification', function () {
    $user = User::where('username', '362358302014')->first(); // Yuda
    if (!$user || !$user->fcm_token) {
        return response()->json(['error' => 'User tidak ada atau belum punya fcm_token'], 400);
    }

    $fcm = new FcmService();
    $result = $fcm->sendNotification(
        $user->fcm_token,
        'Test Notifikasi 🎉',
        'Halo Yuda, ini uji coba notifikasi dari Laravel'
    );

    return response()->json(['success' => true, 'result' => $result]);
});



// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/update-fcm-token', [AuthController::class, 'updateFcmToken'])->middleware('auth:sanctum');





Route::middleware('auth:sanctum')->group(function () {
    Route::post('/documents/upload', [DocumentController::class, 'upload']);
    Route::get('/documents/user', [DocumentController::class, 'listUserDocuments']);
    Route::delete('/documents/cancel/{documentId}', [DocumentController::class, 'cancel']);
    Route::post('/documents/replace/{documentId}', [DocumentController::class, 'replacePdfQr']);
    Route::post('/add/{documentId}', [SignatureController::class, 'addSigner']);
    Route::get('/signature/user', [SignatureController::class, 'listSignRequests']);
    Route::post('/documents/signature/{signToken}', [SignatureController::class, 'processSignature']);
    Route::post('/documents/review/{accessToken}', [DocumentController::class, 'review']);

});


Route::get('/view/{signToken}', [SignatureController::class, 'viewFromPayload']);
Route::get('/documents/download/{accessToken}/{encryptedName}', [DocumentController::class, 'download']);



Route::middleware('auth:sanctum')->get('/auth', function (Request $request) {
    return $request->user();
});
