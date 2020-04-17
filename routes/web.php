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

    //ALLEEN BACKEND (voorlopig)
    Route::get('/', function () {return redirect('/admin/login');});

    Route::prefix(strtolower($langCode))->group(function () {
        //ALLEEN BACKEND (voorlopig)
        Route::get('/', function () {
            return redirect('/admin');
        });

        // Error
        if (config('app.debug') == true) {
            Route::get('/errors/{code}', function (String $code) {
                abort($code);
            });
        }

        // Core
        KJCore::routesSession();

        // Core - Documents
        Route::prefix('document')->group(function() {
            Route::post('/', 'Core\DocumentController@save');
            Route::post('/retrieve', 'Core\DocumentController@retrieve');
            Route::post('/upload', 'Core\DocumentController@upload');
            Route::post('/delete', 'Core\DocumentController@delete');
            Route::post('/add-folder', 'Core\DocumentController@addFolder');
            Route::post('/move', 'Core\DocumentController@move');
            Route::post('/rename', 'Core\DocumentController@rename');

            Route::post('/request', 'Core\DocumentController@request');
            Route::get('/download', '\KJ\Core\controllers\FileRequestController@download');
        });

                // Default (login)
                Route::get('/login', 'Consumer\Auth\LoginController@index')->name('login');
                Route::post('/login', 'Consumer\Auth\LoginController@login')->name('login.submit'); // post bij inlogform
                Route::post('/request', 'Consumer\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.request'); // post bij wachtwoord vergeten form
                Route::get('/reset/{token}', 'Consumer\Auth\ResetPasswordController@showResetForm')->name('password.reset.link'); // link vanuit wachtwoord reset mail
                Route::post('/reset', 'Consumer\Auth\ResetPasswordController@reset')->name('password.reset'); // post vanuit wachtwoord reset form

                Route::get('/logout', 'Consumer\Auth\LoginController@logout')->name('logout');

//                // Home
//                Route::get('/', 'Consumer\Main\MainController@checkIndex');
//                Route::prefix('home')->group(function() {
//                    Route::get('/', 'Consumer\Main\MainController@index');
//                });
//
//                //Client-only routes///////////////////
//                Route::group(['middleware' => 'client'], function () {
//                    // Families
//                    Route::prefix('families')->group(function() {
//                        Route::get('/', 'Consumer\Family\FamilyClientController@index');
//                        Route::get('/allByLoggedInDatatable', 'Consumer\Family\FamilyClientController@allByLoggedInDatatable');
//                        Route::get('/detailRendered/{ID}', 'Consumer\Family\FamilyClientController@detailAsJSON');
//                        Route::post('/', 'Consumer\Family\FamilyClientController@save');
//                    });
//
//                    //Relocations
//                    Route::prefix('relocations')->group(function() {
//                        Route::get('/', 'Consumer\Relocation\RelocationClientController@index');
//                        Route::get('/allByStateDatatable/{ID}', 'Consumer\Relocation\RelocationClientController@allByStateDatatable');
//                        Route::get('/detail/{ID}', 'Consumer\Relocation\RelocationClientController@detail');
//                        Route::post('/', 'Consumer\Relocation\RelocationClientController@save');
//                        //Childern
//                        Route::get('/detail/{ID}/allChildernByDatatable', 'Consumer\Relocation\RelocationClientController@allChildernByDatatable');
//                    });
//                    //End client-only routes///////////////////
//                });
//
//                //Family-only routes///////////////////
//                Route::group(['middleware' => 'family'], function () {
//                    // Questionaire
//                    Route::prefix('questionnaire')->group(function() {
//                        Route::get('/', 'Consumer\Questionnaire\QuestionnaireController@index');
//                        Route::get('/detail/{ID}', 'Consumer\Questionnaire\QuestionnaireController@detail');
//                        Route::post('/', 'Consumer\Questionnaire\QuestionnaireController@save');
//                        Route::post('/question', 'Consumer\Questionnaire\QuestionnaireController@saveQuestion');
//                        Route::post('/contact', 'Consumer\Questionnaire\QuestionnaireController@saveContact');
//
//                        Route::prefix('family')->group(function() {
//                            Route::get('/detail/{ID}', 'Consumer\Questionnaire\QuestionnaireController@detailFamily');
//                        });
//                    });
//
//                    // Relocation
//                    Route::prefix('relocation')->group(function() {
//                        Route::get('/', 'Consumer\Relocation\RelocationController@index');
//                    });
//                });
//
//                // Schools
//                Route::prefix('schools')->group(function() {
//                    Route::get('/', 'Consumer\School\SchoolController@index');
//                    Route::get('/{ID}', 'Consumer\School\SchoolController@detail');
//                });
//
//                // Countries
//                Route::prefix('country')->group(function() {
//                    Route::get('/city/allByCountry/{ID}', 'Consumer\Country\CityController@allByCountry');
//                });
//
//                // Static pages
//                Route::get('/privacy', 'Consumer\Pages\PrivacyController@index');
//                Route::get('/support', 'Consumer\Pages\SupportController@index');
//                Route::get('/contact', 'Consumer\Pages\ContactController@index');
//
//                // Profile
//                Route::prefix('profile')->group(function() {
//                    Route::get('/', 'Consumer\Profile\ProfileController@index');
//                    Route::post('/', 'Consumer\Profile\ProfileController@save');
//                });

        Route::prefix('assets/kj/localization')->group(function() {
            KJLocalization::routesAssets();
        });

        Route::prefix('assets/kj/core')->group(function() {
            KJCore::routesAssets();
        });

        KJLocalization::routesUser();

    });
}