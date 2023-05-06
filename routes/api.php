<?php

use App\Http\Controllers\API\Admin\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware'=>['api'/*,'checkPassword'*/]], function (){
   Route::post('m','WorkerSallaryController@m');
   Route::post('control','WorkerSallaryController@control');
   
   Route::post('res','WorkerSallaryController@d4000');
    Route::get('mobile','WorkerSallaryController@api');
    Route::get('workergeneratepdf/{project_id}', 'PdfController@workergeneratePdf');
    Route::get('workerdownloadpdf/{project_id}', 'PdfController@workerdownloadPdf');
    Route::post('login','AuthController@login');
    Route::get('profile','AuthController@profile');
    Route::post('logout','AuthController@logout');
    Route::post('register','AuthController@register')->middleware(['auth.guard:admin-api']);;
    Route::post('updateuser/{id}','AuthController@updateuser')->middleware(['auth.guard:admin-api']);;
    Route::group(['prefix' => 'admin','namespace'=>'Admin'],function (){
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('logout','AuthController@logout') -> middleware(['auth.guard:admin-api']);
    });
});

Route::group(['middleware'=>['auth:api'/*,'checkPassword'*/]], function (){
    ################################ Worker Route ###############################################
    Route::resource('worker', 'WorkerController')->except(['show','edit','create','get']);
    Route::get('workerproject/{project_id}', 'WorkerController@get');
    Route::post('storeRegister','WorkerController@storeRegister');
    ################################ Project Route ###############################################
    Route::resource('project', 'ProjectController')->except(['show','edit','create','alluser']);
    Route::get('project/get', 'ProjectController@get');
    Route::get('test','ProjectController@test');
    ################################ Sallary Route ###############################################
    Route::get('sallary/{project_id}/{date}', 'WorkerSallaryController@get');
    Route::post('sallarydaily/{id}', 'WorkerSallaryController@sallarydaily');
    Route::get('sallarydailydetails/{id}', 'WorkerSallaryController@sallarydailydetails');  
    Route::post('deletesallary/{id}', 'WorkerSallaryController@destroysallary');
    ################################ Expenses Route ##############################################
    Route::get('monthlyexpenses/{project_id}', 'ExpensesController@get');
    Route::post('monthlyexpenses/store/{project_id}', 'ExpensesController@storeExpenses');
    Route::post('monthlyexpenses/update/{id}', 'ExpensesController@updateExpenses');
    Route::post('monthlyexpenses/delete/{id}', 'ExpensesController@deleteExpenses');
    Route::get('monthlyexpenses/report/{date}/{project_id}', 'ExpensesController@report');
    ################################ Payment Route ################################################
    Route::get('payments/get/{project}/{worker_id}','PaymentsController@getPayments');
    Route::post('payments/store/{project}','PaymentsController@storePayments');
    Route::post('payments/update/{id}','PaymentsController@updatePayments');
    Route::post('payments/delete/{id}','PaymentsController@deletePayments');
    ################################ Worker account statement ##################################
    Route::get('workersaccount/{project_id}/{date}','WorkerAccountController@workers');
    Route::get('workeraccount/{project_id}/{date}/{worker_id}','WorkerAccountController@worker');
    Route::get('projectsummary/{project_id}','ProjectController@summary');
    ################################# NOTE ROUTE ###########################################
    Route::resource('note', 'NoteController')->except(['show','edit','create']);
    ################################## Route BUDGET ############################################
    Route::post('budgetStore/{project_id}','Admin\ProjectBudgetController@store');
    Route::get('budget/{project_id}','Admin\ProjectBudgetController@get');
    Route::post('budget/{id}','Admin\ProjectBudgetController@update');
    Route::post('budgetdelete/{id}','Admin\ProjectBudgetController@delete');
    ############################## SEGAL ROUTE #######################################
    Route::resource('segal', 'SegalController')->except(['show','edit','create']);
    Route::get('segal/{project_id}/{date}/{segal_id}', 'WorkerSegalController@get');
    Route::post('segaldaily/{id}', 'WorkerSegalController@sallarydaily');
    Route::get('segaldailydetails/{id}', 'WorkerSegalController@sallarydailydetails');
    Route::get('segalreport/{project_id}/{segal_id}/{date}', 'WorkerSegalController@reprotall');   
});

Route::group(['middleware'=>['auth.guard:admin-api'/*,'checkPassword'*/],'prefix' => 'admin','namespace'=>'Admin'],function (){

    Route::get('alluser', 'UsersController@alluser');
    Route::post('deleteuser/{id}','UsersController@deleteuser');

    Route::get('projectunderway','ProjectController@underway');
    Route::get('projectfinshed','ProjectController@finshed');
    Route::get('projectsummary/{project_id}','ProjectController@summary');
});

Route::group(['middleware'=>['auth.guard:worker-api'/*,'checkPassword'*/],'prefix' => 'worker','namespace'=>'Worker'],function (){
    Route::post('change', 'AccountController@change');
    Route::get('/{date}', 'AccountController@account');
    Route::get('sallary/{date}', 'WorkerSallaryController@get');
    Route::post('sallarydaily/{id}', 'WorkerSallaryController@sallarydaily');
    Route::get('sallarydailydetails/{id}', 'WorkerSallaryController@sallarydailydetails');

    Route::get('segal/{date}/{segal_id}', 'WorkerSegalController@get');
    Route::post('segaldaily/{id}', 'WorkerSegalController@sallarydaily');


    
});


