<?php

use App\Http\Controllers\ZohoController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('app'));

Route::get('/get-valid-token', [ZohoController::class, 'getValidToken']);
Route::post('/create-account', [ZohoController::class, 'createAccount']);
Route::post('/create-deal', [ZohoController::class, 'createDeal']);
Route::get('/get-accounts', [ZohoController::class, 'getAccounts']);
Route::get('/get-deals', [ZohoController::class, 'getDeals']);
Route::get('/get-contacts', [ZohoController::class, 'getContacts']);
Route::get('/get-campaigns', [ZohoController::class, 'getCampaigns']);
