<?php

use App\Http\Controllers\ApiController;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('reset-league', [ApiController::class, 'resetLeague']);
Route::get('createFixture', [ApiController::class, 'createFixture']);
Route::get('standings', [ApiController::class, 'getStandings']);
Route::get('fixture', [ApiController::class, 'getFixture']);

Route::post('play-league', [ApiController::class, 'postPlayLeague']);
Route::post('play-week', [ApiController::class, 'postPlayWeek']);
Route::post('update-match', [ApiController::class, 'postUpdateMatch']);

Route::get('{function}', [ApiController::class, 'getFunction']);
