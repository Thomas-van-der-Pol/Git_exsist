<?php

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

/*
 *  Set up locale and locale_prefix if other language is selected
 */

$alt_langs = array_column(config('language.langs'), 'CODE');
if (in_array(strtoupper(Request::segment(1)), $alt_langs)) {
    App::setLocale(Request::segment(1));
}

array_push($alt_langs, '');
foreach($alt_langs as $langCode) {

    Route::prefix(strtolower($langCode))->group(function () {

        // Shared documents
        Route::prefix('shared-documents')->group(function () {
            Route::get('/login', 'Consumer\Document\Auth\LoginController@index')->name('document.login');
            Route::post('/login', 'Consumer\Document\Auth\LoginController@login')->name('document.login.submit'); // post bij inlogform
            Route::get('/logout', 'Consumer\Document\Auth\LoginController@logout')->name('document.logout');

            Route::get('/files/{GUID}', 'Consumer\Document\DocumentController@indexCustom')->name('document.share');

            Route::post('/request', 'Core\DocumentController@request');
            Route::get('/download', '\KJ\Core\controllers\FileRequestController@download');
        });

    });
}