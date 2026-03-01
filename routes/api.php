<?php

use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\HeadOfFamilyController;
use App\Http\Controllers\SocialAssistanceController;
use App\Http\Controllers\SocialAssistanceRecipientController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('users', UserController::class);

Route::apiResource('head-of-family', HeadOfFamilyController::class);

Route::apiResource('family-member', FamilyMemberController::class);

Route::apiResource('social-assistance', SocialAssistanceController::class);

Route::apiResource('social-assistance-recipient', SocialAssistanceRecipientController::class);