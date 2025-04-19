

<?php

use App\Http\Controllers\Api\TelegramController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


Route::middleware(['api','auth:sanctum'])->group(function(){
        Route::post('/start', [TelegramController::class,'start']);
        Route::post('/charge', [TelegramController::class,'charge']);
        Route::post('/ichancy', [TelegramController::class,'ichancy']);
        Route::post('/checkbalance', [TelegramController::class,'checkbalance']);
        Route::post('/newichaccount', [TelegramController::class,'newichaccount']);
        Route::post('/getMyBalance', [TelegramController::class,'getMyBalance']);
        Route::post('/charge_ichancy', [TelegramController::class,'charge_ichancy']);
        Route::post('/withdraw_ichancy', [TelegramController::class,'withdraw_ichancy']);
        Route::post('/withdraw', [TelegramController::class,'withdraw']);
        Route::post('/undo_withdraw', [TelegramController::class,'undo_withdraw']);
        Route::post('/getIchancyBalance', [TelegramController::class,'getIchancyBalance']);
        Route::post('/ex_ich_charge', [TelegramController::class,'ex_ich_charge']);
        Route::post('/ex_withdraw', [TelegramController::class,'ex_withdraw']);
        Route::post('/chargeBemo', [TelegramController::class,'chargeBemo']);
        Route::post('/ex_bemo_charge', [TelegramController::class,'ex_bemo_charge']);
        Route::post('/reject_bemo_charge', [TelegramController::class,'reject_bemo_charge']);
        // Route::post('/newichaccount_v2', [TelegramController::class,'newichaccount_v2']);
        //testing
        Route::post('/charge1', [TelegramController::class,'charge1']);
    });

Route::middleware('api')->group(function(){
    Route::post('/test', [TelegramController::class,'test']);
});
Route::middleware('api')->group(function(){
    Route::post('/newichaccount', [TelegramController::class,'newichaccount']);
});
Route::middleware('api')->group(function(){
    Route::post('/newichaccount_v2', [TelegramController::class,'newichaccount_v2']);
});
// // مجرد تضمين التوكين يتم تلقائيا الحصول على بيانات المستخدم المرل للطب
// Route::get('/testSanctum', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::get('/testSanctum', function (Request $request) {
//     if($request->user()->tokenCan('categories:delete')){
//         return ["user" => $request->user(),'can'=>'yes delete'];
//     }
// })->middleware('auth:sanctum');



?>
