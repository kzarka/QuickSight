<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [Controller::class, 'index']);


Route::get('generate/{id}', [Controller::class, 'generate'])->middleware(['cors']);
Route::get('generate-console', [Controller::class, 'generateConsole'])->middleware(['cors']);
Route::get('register', [Controller::class, 'register']);
Route::get('build', [Controller::class, 'buildCsv']);
Route::get('build/maintain', [Controller::class, 'buildMaintainLog']);
