<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
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

        // Admin
        Route::prefix('admin')->group(function() {

            // Login
            Route::get('/login', 'Admin\Auth\LoginController@index')->name('admin.login');
            Route::get('/forgot', 'Admin\Auth\ForgotPasswordController@index')->name('admin.forgot');
            Route::post('/login', 'Admin\Auth\LoginController@login')->name('admin.login.submit'); // post bij inlogform
            Route::post('/request', 'Admin\Auth\ForgotPasswordController@sendResetLinkEmail')->name('admin.password.request'); // post bij wachtwoord vergeten form
            Route::get('/reset/{token}', 'Admin\Auth\ResetPasswordController@showResetForm')->name('admin.password.reset.link'); // link vanuit wachtwoord reset mail
            Route::post('/reset', 'Admin\Auth\ResetPasswordController@reset')->name('admin.password.reset'); // post vanuit wachtwoord reset form
            Route::get('/logout', 'Admin\Auth\LoginController@logout')->name('admin.logout');

            // Main
            Route::get('/', 'Admin\Dashboard\MainController@index');
            Route::post('/notification', 'Admin\Dashboard\MainController@notification');

            Route::prefix('communicator')->group(function() {
                Route::get('/download', 'Admin\Communicator\CommunicatorController@download');
            });

            // CRM
            Route::prefix('crm')->group(function() {

                // Client
                Route::prefix('relation')->group(function() {
                    Route::get('/', 'Admin\CRM\RelationController@index');
                    Route::get('/allDatatable', 'Admin\CRM\RelationController@allDatatable');
                    Route::get('/detail/{ID}', 'Admin\CRM\RelationController@detail');
                    Route::post('/detailScreen', 'Admin\CRM\RelationController@detailScreen');
                    Route::post('/', 'Admin\CRM\RelationController@save');
                    Route::delete('/{ID}', 'Admin\CRM\RelationController@delete');
                    Route::post('/debtor/{ID}', 'Admin\CRM\RelationController@generateDebtornumber');
                    Route::post('/creditor/{ID}', 'Admin\CRM\RelationController@generateCreditornumber');

                    Route::get('/modal', 'Admin\CRM\RelationController@indexModal');

                    // Contact
                    Route::prefix('contact')->group(function() {
                        Route::get('/allByRelationDatatable/{ID}', 'Admin\CRM\Contact\ContactController@allByRelationDatatable');
                        Route::get('/detailRendered/{ID}', 'Admin\CRM\Contact\ContactController@detailDefault');
                        Route::get('/allByRelation/{ID}', 'Admin\CRM\Contact\ContactController@allByRelation');
                        Route::post('/', 'Admin\CRM\Contact\ContactController@save');
                        Route::post('/anonimyze', 'Admin\CRM\Contact\ContactController@anonimyze');
                        Route::delete('/{ID}', 'Admin\CRM\Contact\ContactController@delete');

                        Route::prefix('modal')->group(function () {
                            Route::get('/', 'Admin\CRM\Contact\ContactController@indexRendered');
                        });
                    });
                });

                Route::prefix('contact')->group(function() {
                    Route::get('/', 'Admin\CRM\ContactController@index');
                    Route::get('/allDatatable', 'Admin\CRM\ContactController@allDatatable');
                    Route::get('/detailRelation', 'Admin\CRM\ContactController@detailRelation');

                    Route::get('/modal', 'Admin\CRM\ContactController@indexModal');
                });

                // Address
                Route::prefix('address')->group(function() {
                    Route::get('/allByRelationDatatable/{ID}', 'Admin\CRM\AddressController@allByRelationDatatable');
                    Route::get('/allByRelation/{ID}', 'Admin\CRM\AddressController@allByRelation');
                    Route::get('/detailRendered/{ID}', 'Admin\CRM\AddressController@detailAsJSON');
                    Route::post('/', 'Admin\CRM\AddressController@save');
                    Route::delete('/{ID}', 'Admin\CRM\AddressController@delete');
                    Route::post('/replicate', 'Admin\CRM\AddressController@replicate');
                });

                // Project
                Route::prefix('project')->group(function() {
                    Route::get('/allByRelationDatatable/{ID}', 'Admin\Project\ProjectController@allByRelationDatatable');
                });

                // Invoice
                Route::prefix('invoice')->group(function() {
                    Route::get('/allByRelationDatatable/{ID}', 'Admin\Finance\InvoiceOverviewController@allByRelationDatatable');
                });
            });

            // PROJECT
            Route::prefix('project')->group(function() {
                Route::get('/', 'Admin\Project\ProjectController@index');
                Route::post('/detailScreenOverview', 'Admin\Project\ProjectController@detailScreenOverview');
                Route::get('/allByWorkflowDatatable/{TYPE_ID}/{ID}', 'Admin\Project\ProjectController@allByWorkflowDatatable');
                Route::get('/detail/{ID}', 'Admin\Project\ProjectController@detail');
                Route::post('/detailScreen', 'Admin\Project\ProjectController@detailScreen');
                Route::post('/', 'Admin\Project\ProjectController@save');
                Route::delete('/{ID}', 'Admin\Project\ProjectController@delete');
                Route::get('/data/{ID}', 'Admin\Project\ProjectController@find');

                // Product
                Route::prefix('product')->group(function() {
                    Route::get('/detailRendered/{ID}', 'Admin\Project\Product\ProductProjectController@detailAsJSON');
                    Route::get('/allByProjectProductDatatable/{ID}', 'Admin\Project\Product\ProductProjectController@allByProjectProductDatatable');
                    Route::get('/allByProjectProductTotal/{ID}', 'Admin\Project\Product\ProductProjectController@allByProjectProductTotal');
                    Route::get('/editable/{ID}', 'Admin\Project\Product\ProductProjectController@itemEditable');
                    Route::post('/save', 'Admin\Project\Product\ProductProjectController@save');

                    Route::post('/addProduct', 'Admin\Project\Product\ProductProjectController@addProduct');
                    Route::delete('/{ID}', 'Admin\Project\Product\ProductProjectController@delete');
                });

                Route::prefix('modal')->group(function() {
                    Route::get('/', 'Admin\Project\ProjectController@modal');
                    Route::get('/allByModalProjectDatatable', 'Admin\Project\ProjectController@allByModalProjectDatatable');
                });

                // Invoice
                Route::prefix('invoice')->group(function() {
                    Route::get('/allByProjectDatatable/{ID}', 'Admin\Finance\InvoiceOverviewController@allByProjectDatatable');
                });
            });


            // ASSORTMENT
            Route::prefix('product')->group(function() {
                Route::get('/', 'Admin\Assortment\ProductController@index');
                Route::get('/allDatatable', 'Admin\Assortment\ProductController@allDefaultDatatable');
                Route::get('/allByProjectDatatable/{ID}', 'Admin\Assortment\ProductController@allByProjectDatatable');
                Route::get('/detail/{ID}', 'Admin\Assortment\ProductController@detail');
                Route::post('/detailScreen', 'Admin\Assortment\ProductController@detailScreen');
                Route::post('/', 'Admin\Assortment\ProductController@save');
                Route::post('/upload', 'Admin\Assortment\ProductController@uploadFile');
                Route::delete('/{ID}', 'Admin\Assortment\ProductController@delete');
                Route::delete('/deleteFile/{ID}', 'Admin\Assortment\ProductController@deleteFile');

                Route::get('/data/{ID}', 'Admin\Assortment\ProductController@find');

                Route::prefix('modal')->group(function () {
                    Route::get('/', 'Admin\Assortment\ProductController@indexRendered');
                });
            });

            // TASKS
            Route::prefix('tasks')->group(function() {
                Route::get('/', 'Admin\Tasks\TasksController@index');
                Route::get('/detail/{ID}', 'Admin\Tasks\TasksController@detail')->name('admin.task.detail');
                Route::post('/detailScreen', 'Admin\Tasks\TasksController@detailScreen');
                Route::post('/', 'Admin\Tasks\TasksController@save');
                Route::post('/retrieveTasks', 'Admin\Tasks\TasksController@retrieveTasks');
                Route::get('/modal/{ID}', 'Admin\Tasks\TasksController@modal');
                Route::get('/functions/modal', 'Admin\Tasks\TasksController@functionsModal');
                Route::get('/custommap/modal/{ID}', 'Admin\Tasks\CustomMapController@modal');
                Route::post('/saveMap', 'Admin\Tasks\CustomMapController@save');

                Route::post('/setSubscription', 'Admin\Tasks\TasksController@setSubscription');
                Route::post('/setDone', 'Admin\Tasks\TasksController@setDone');
                Route::post('/shiftDeadline', 'Admin\Tasks\TasksController@shiftDeadline');
                Route::post('/copyToMap', 'Admin\Tasks\TasksController@copyToMap');
                Route::post('/connectEmployee', 'Admin\Tasks\TasksController@connectEmployee');
                Route::delete('/{ID}', 'Admin\Tasks\TasksController@delete');

                Route::prefix('list')->group(function() {
                    Route::get('/modal/{ID}', 'Admin\Tasks\TasksController@taskListModal');
                    Route::post('/', 'Admin\Tasks\TasksController@saveTaskList');
                });
            });

            // INVOICING
            Route::prefix('invoice')->group(function() {
                Route::prefix('prepare')->group(function() {
                    Route::get('/', 'Admin\Finance\Prepare\InvoicePrepareController@index');
                    Route::get('/allDatatable', 'Admin\Finance\Prepare\InvoicePrepareController@allDatatable');
                    Route::get('/detail/{ids}', 'Admin\Finance\Prepare\InvoicePrepareController@customDetailAsJSON');
                    Route::get('/allDetailDatatable/{ids}', 'Admin\Finance\Prepare\InvoicePrepareController@allDetailDatatable');

                    Route::post('/process', 'Admin\Finance\Prepare\InvoicePrepareController@process');
                    Route::post('/createInvoices', 'Admin\Finance\Prepare\InvoicePrepareController@createInvoices');
                });

                Route::get('/', 'Admin\Finance\InvoiceOverviewController@index');
                Route::post('/detailScreenOverview', 'Admin\Finance\InvoiceOverviewController@detailScreen');
                Route::get('/allByStateDatatable/{ID}', 'Admin\Finance\InvoiceOverviewController@allByStateDatatable');
                Route::post('/generateBulk', 'Admin\Finance\InvoiceOverviewController@generateBulk');
                Route::post('/reminderBulk', 'Admin\Finance\InvoiceOverviewController@reminderBulk');

                Route::get('/detail/{ID}', 'Admin\Finance\InvoiceController@detail');
                Route::post('/detailScreen', 'Admin\Finance\InvoiceController@detailScreen');
                Route::post('/', 'Admin\Finance\InvoiceController@save');
                Route::get('/previewPDF/{ID}', 'Admin\Finance\InvoiceController@previewPDF');
                Route::post('/sendInvoice', 'Admin\Finance\InvoiceController@sendInvoice');
                Route::post('/sendInvoiceReminder', 'Admin\Finance\InvoiceController@sendInvoiceReminder');
                Route::delete('/{ID}', 'Admin\Finance\InvoiceController@delete');

                Route::prefix('line')->group(function () {
                    Route::get('/allByInvoiceDatatable/{ID}', 'Admin\Finance\InvoiceLineController@allByInvoiceDatatable');
                    Route::get('/detailRendered/{ID}', 'Admin\Finance\InvoiceLineController@detailAsJSON');
                    Route::post('/', 'Admin\Finance\InvoiceLineController@save');
                    Route::delete('/{ID}', 'Admin\Finance\InvoiceLineController@delete');
                });

                Route::prefix('scheme')->group(function () {
                    Route::get('/allByProductDatatable/{ID}', 'Admin\Finance\InvoiceSchemeController@allByProductDatatable');
                    Route::get('/detailRendered/{ID}', 'Admin\Finance\InvoiceSchemeController@detailAsJSON');
                    Route::post('/', 'Admin\Finance\InvoiceSchemeController@save');
                    Route::delete('/{ID}', 'Admin\Finance\InvoiceSchemeController@delete');

                    Route::prefix('project')->group(function () {
                        Route::get('/allByProductDatatable/{ID}', 'Admin\Project\Product\InvoiceSchemeController@allByProjectProductDatatable');
                        Route::get('/detailRendered/{ID}', 'Admin\Project\Product\InvoiceSchemeController@DetailAsJSON');
                        Route::post('/', 'Admin\Project\Product\InvoiceSchemeController@save');
                        Route::delete('/{ID}', 'Admin\Project\Product\InvoiceSchemeController@delete');
                    });
                });
            });

            Route::prefix('accountancy')->group(function() {
                Route::get('/', 'Admin\Finance\Exact\ExactResource@index');
                Route::post('/detailScreenOverview', 'Admin\Finance\Exact\ExactResource@detailScreen');
                Route::get('/redirect', 'Admin\Finance\Exact\ExactResource@exactRedirect');

                Route::prefix('debtor')->group(function() {
                    Route::get('/allDatatable', 'Admin\Finance\Exact\Models\DebtorResource@allDatatable');
                    Route::post('/export', 'Admin\Finance\Exact\Models\DebtorResource@export');
                });

                Route::prefix('creditor')->group(function() {
                    Route::get('/allDatatable', 'Admin\Finance\Exact\Models\CreditorResource@allDatatable');
                    Route::post('/export', 'Admin\Finance\Exact\Models\CreditorResource@export');
                });

                Route::prefix('invoice')->group(function() {
                    Route::get('/allDatatable', 'Admin\Finance\Exact\Models\InvoiceResource@allDatatable');
                    Route::get('/detailRendered/{ID}', 'Admin\Finance\Exact\Models\InvoiceResource@detailAsJSON');
                    Route::post('/export', 'Admin\Finance\Exact\Models\InvoiceResource@export');
                });

                Route::prefix('receivable')->group(function() {
                    Route::post('/import', 'Admin\Finance\Exact\Models\ReceivablesResource@import');
                });
            });

            // Profile
            Route::prefix('profile')->group(function() {
                Route::get('/', 'Admin\Profile\ProfileController@index');
                Route::post('/', 'Admin\Profile\ProfileController@save');
            });

            // Settings
            Route::prefix('settings')->group(function() {
                // Groups
                Route::prefix('group')->group(function() {
                    Route::get('/{ID}', 'Core\Setting\SettingController@detail');
                    Route::post('/', 'Core\Setting\SettingController@save');
                });

                // Users
                Route::prefix('user')->group(function () {
                    Route::get('/', 'Admin\Settings\User\UserController@index');
                    Route::get('/allDatatable', 'Admin\Settings\User\UserController@allDatatable');
                    Route::get('/detail/{ID}', 'Admin\Settings\User\UserController@detail');
                    Route::post('/detailScreen', 'Admin\Settings\User\UserController@detailScreen');
                    Route::post('/', 'Admin\Settings\User\UserController@save');
                    Route::post('/anonimyze', 'Admin\Settings\User\UserController@anonimyze');
                    Route::post('/resetPassword', 'Admin\Settings\User\UserController@resetPassword');
                    Route::delete('/{ID}', 'Admin\Settings\User\UserController@delete');
                    Route::get('/currentUser', 'Admin\Settings\User\UserController@currentUser');
                });

                // Financieel
                Route::prefix('finance')->group(function () {
                    Route::get('/', 'Admin\Settings\Finance\FinanceController@index');
                    Route::get('/allDatatable', 'Admin\Settings\Finance\FinanceController@allDatatable');
                    Route::get('/detail/{ID}', 'Admin\Settings\Finance\FinanceController@detail');
                    Route::post('/detailScreen', 'Admin\Settings\Finance\FinanceController@detailScreen');
                    Route::post('/', 'Admin\Settings\Finance\FinanceController@save');
                    Route::delete('/{ID}', 'Admin\Settings\Finance\FinanceController@delete');
                    // File upload (pdf paper)
                    Route::post('/upload', 'Admin\Settings\Finance\FinanceController@uploadFile');
                    Route::delete('/deleteFile/{ID}', 'Admin\Settings\Finance\FinanceController@deleteFile');

                    Route::prefix('ledger')->group(function () {
                        Route::get('/allByLabelDatatable/{ID}', 'Admin\Settings\Finance\LedgerController@allByLabelDatatable');
                        Route::get('/detailRendered/{ID}', 'Admin\Settings\Finance\LedgerController@detailAsJSON');
                        Route::post('/', 'Admin\Settings\Finance\LedgerController@save');
                        Route::delete('/{ID}', 'Admin\Settings\Finance\LedgerController@delete');
                    });

                    Route::prefix('vat')->group(function () {
                        Route::get('/allByLabelDatatable/{ID}', 'Admin\Settings\Finance\VatController@allByLabelDatatable');
                        Route::get('/detailRendered/{ID}', 'Admin\Settings\Finance\VatController@detailAsJSON');
                        Route::post('/', 'Admin\Settings\Finance\VatController@save');
                        Route::delete('/{ID}', 'Admin\Settings\Finance\VatController@delete');
                    });
                });

                // Role & Permission
                Route::prefix('role')->group(function () {
                    Route::get('/', 'Admin\Settings\Role\RoleController@index');
                    Route::get('/allDatatable', 'Admin\Settings\Role\RoleController@allDatatable');
                    Route::get('/detailRendered/{ID}', 'Admin\Settings\Role\RoleController@detailAsJSON');
                    Route::post('/', 'Admin\Settings\Role\RoleController@save');
                    Route::delete('/{ID}', 'Admin\Settings\Role\RoleController@delete');
                });

                // Language
                Route::prefix('language')->group(function () {
                    Route::get('/', 'Admin\Settings\Language\LanguageController@index');
                    Route::get('/allDatatable', 'Admin\Settings\Language\LanguageController@allDatatable');
                    Route::get('/detailRendered/{ID}', 'Admin\Settings\Language\LanguageController@detailAsJSON');
                    Route::post('/', 'Admin\Settings\Language\LanguageController@save');
                });

                // Host
                Route::prefix('host')->group(function () {
                    Route::get('/', 'Admin\Settings\Host\HostController@index');
                    Route::get('/allDatatable', 'Admin\Settings\Host\HostController@allDatatable');
                    Route::get('/detailRendered/{ID}', 'Admin\Settings\Host\HostController@detailAsJSON');
                    Route::post('/', 'Admin\Settings\Host\HostController@save');

                    Route::post('/getPrintersByHost', 'Admin\Settings\Host\HostController@getPrintersByHost');
                });

                Route::prefix('tasklist')->group(function () {
                    Route::get('/', 'Admin\Settings\TaskList\TaskListController@index');
                    Route::get('/detail/{ID}', 'Admin\Settings\TaskList\TaskListController@detail');
                    Route::get('/allDatatable', 'Admin\Settings\TaskList\TaskListController@allDatatable');
                    Route::post('/detailScreen', 'Admin\Settings\TaskList\TaskListController@detailScreen');
                    Route::post('/', 'Admin\Settings\TaskList\TaskListController@save');
                    Route::delete('/{ID}', 'Admin\Settings\TaskList\TaskListController@delete');
                });

                // Translation
                KJLocalization::routesManagement();
            });

            // Dropdown value
            Route::prefix('dropdownvalue')->group(function () {
                Route::get('/', 'Core\DropdownValueController@indexRendered');
                Route::get('/select', 'Core\DropdownValueController@indexSelectableRendered');
                Route::get('/allByTypeRendered/{ID}', 'Core\DropdownValueController@allByTypeRendered');
                Route::get('/allByTypeDatatable/{ID}', 'Core\DropdownValueController@allByTypeDatatable');
                Route::get('/detailRendered/{ID}', 'Core\DropdownValueController@detailAsJSON');
                Route::post('/', 'Core\DropdownValueController@save');
            });

            Route::get('/changeLanguage/{locale}', 'Admin\Settings\Language\LanguageController@switchLang');
        });
    });
}