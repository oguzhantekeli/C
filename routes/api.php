<?php

use App\Http\Controllers\ApiController;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('reset-league', [ApiController::class, 'resetLeague']);
Route::get('standings', [ApiController::class, 'getStandings']);
Route::get('fixture', [ApiController::class, 'getFixture']);

Route::post('play-league', [ApiController::class, 'postPlayLeague']);
Route::post('play-week', [ApiController::class, 'postPlayWeek']);
Route::post('update-match', [ApiController::class, 'postUpdateMatch']);
