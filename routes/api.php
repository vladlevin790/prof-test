<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FilesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/files', [FilesController::class, 'store'])->middleware('auth:sanctum');
Route::post('/files/{id}',[FilesController::class,'updateFile'])->middleware('auth:sanctum');
Route::delete('/files/{id}',[FilesController::class, 'destroy'])->middleware('auth:sanctum');
Route::get('/files/{id}',[FilesController::class,'download'])->middleware('auth:sanctum');




Route::post('/registration',[AuthController::class,'signup']);
Route::post('/login',[AuthController::class,'login']);