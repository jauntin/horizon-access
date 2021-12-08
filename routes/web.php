<?php

use Illuminate\Support\Facades\Route;
use Jauntin\HorizonAccess\Http\Controllers\GithubController;

Route::get(config('horizon-access.redirect'), [GithubController::class, 'redirect']);
Route::get(config('horizon-access.callback'), [GithubController::class, 'callback']);
