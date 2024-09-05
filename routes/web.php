<?php
use App\Http\Controllers\MetricController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [MetricController::class, 'index'])->name('pagespeed');
Route::post('/pagespeed/metrics', [MetricController::class, 'getMetrics'])->name('get.pagespeed.metrics');
Route::post('/pagespeed/metrics/save', [MetricController::class, 'saveMetrics'])->name('post.pagespeed.metrics');
Route::get('/metrics-history', [MetricController::class, 'getHistory'])->name('metrics.history');