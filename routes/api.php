<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\api\GoalController;
use App\Http\Controllers\api\GoalProgressController;
use App\Http\Controllers\api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/send-password-reset-link', [AuthController::class, 'sendResetLinkEmail']);

Route::middleware('auth:sanctum')->group(function () {

    // User
    Route::get('/user', [UserController::class, 'getUser']);
    Route::post('/setUser', [userController::class, 'setProfile']);
    Route::post('/updateUser', [userController::class, 'updateProfile']);

    // Goal
    Route::get('/getAllGoals', [GoalController::class, 'getAllGoals']);
    Route::post('/setGoal', [GoalController::class, 'setGoal']);
    Route::post('/updateGoal/{goal_id}', [GoalController::class, 'updateGoal']);
    Route::delete('/deleteGoal/{goal_id}', [GoalController::class, 'deleteGoal']);

    // Progress
    Route::post('/updateProgress/{goal_id}', [GoalProgressController::class, 'updateProgress']);
    Route::get('/getProgress/{goal_id}', [GoalProgressController::class, 'getProgress']);
    Route::delete('/deleteProgress/{goal_id}', [GoalProgressController::class, 'deleteProgress']);


    // Reminders

    Route::post('logout', [AuthController::class, 'logout']);
});
