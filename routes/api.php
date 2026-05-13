<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\SelectorController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Diagnosis\SgsstDiagnosisController;
use App\Http\Controllers\Ia\DocumentCompletionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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

Route::group(['prefix' => 'auth'], function () {
    Route::post('authenticate', [AuthController::class, 'authenticate']);
});

Route::post('/ops/sync-mongo-menus', function (Request $request) {
    $providedKey = $request->query('key') ?: $request->header('X-Ops-Key');
    $expectedKey = env('OPS_SYNC_KEY');

    abort_unless($expectedKey && hash_equals((string) $expectedKey, (string) $providedKey), 403);

    $results = [];
    $commands = [
        ['name' => 'ia:mongo-init-schemas', 'params' => []],
        ['name' => 'ia:mongo-sync-catalogs-from-sql', 'params' => ['--truncate' => true]],
        ['name' => 'ia:mongo-sync-users-from-sql', 'params' => ['--truncate' => true]],
    ];

    foreach ($commands as $command) {
        $exitCode = Artisan::call($command['name'], $command['params']);
        $results[] = [
            'command' => $command['name'],
            'exit_code' => $exitCode,
            'output' => trim(Artisan::output()),
        ];
    }

    return response()->json([
        'ok' => true,
        'results' => $results,
    ]);
});


Route::group(["middleware" => ["auth:api"]], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::get("refresh", [AuthController::class, "refresh"]);
        Route::get("logout", [AuthController::class, "logout"]);
        Route::get('me', [AuthController::class, 'me']);
    });

    Route::group(['prefix' => 'user'], function () {
        Route::get('', [UserController::class, 'all']);
        Route::get('/{id}', [UserController::class, 'find'])->whereNumber('id');
        Route::get('/{col}/{id}', [UserController::class, 'findBy'])->where('col', '[A-Za-z_]+');
        Route::post('', [UserController::class, 'create']);
        Route::put('/{id}', [UserController::class, 'update'])->whereNumber('id');
        Route::delete('/{id}', [UserController::class, 'delete'])->whereNumber('id');
    });

    Route::group(['prefix' => 'profile'], function () {
        Route::get('', [ProfileController::class, 'all']);
        Route::get('/{id}', [ProfileController::class, 'find']);
        Route::get('/{col}/{id}', [ProfileController::class, 'findBy']);
    });

    Route::group(['prefix' => 'company'], function () {
        Route::get('', [CompanyController::class, 'all']);
        Route::get('/{id}', [CompanyController::class, 'find']);
        Route::get('/{col}/{id}', [CompanyController::class, 'findBy']);
        Route::post('', [CompanyController::class, 'create']);
        Route::put('/{id}', [CompanyController::class, 'update']);
        Route::delete('/{id}', [CompanyController::class, 'delete']);
    });

    Route::group(['prefix' => 'employee'], function () {
        Route::get('', [EmployeeController::class, 'all']);
        Route::get('/{id}', [EmployeeController::class, 'find']);
        Route::get('/{col}/{id}', [EmployeeController::class, 'findBy']);
        Route::post('', [EmployeeController::class, 'create']);
        Route::put('/{id}', [EmployeeController::class, 'update']);
        Route::delete('/{id}', [EmployeeController::class, 'delete']);
    });

    Route::group(['prefix' => 'conversation'], function () {
        // Obtener todas las conversaciones del usuario autenticado
        Route::get('/', [MessageController::class, 'getMyConversations']);

        // Obtener los detalles de una conversación específica
        Route::get('/{conversationId}', [MessageController::class, 'show']);

        // Crear una nueva conversación con un mensaje inicial
        Route::post('/', [MessageController::class, 'createConversation']);

        // Responder a una conversación existente
        Route::post('/{conversationId}/reply', [MessageController::class, 'reply']);

        // Rutas para la gestión de estados y borrado suave
        Route::post('/{conversationId}/status', [MessageController::class, 'updateStatus']);
        Route::put('/{conversationId}/archive', [MessageController::class, 'archive']);
        Route::put('/{conversationId}/unarchive', [MessageController::class, 'unarchive']);
        Route::put('/{conversationId}/restore', [MessageController::class, 'restore']);
        Route::delete('/{conversationId}', [MessageController::class, 'delete']);
    });

    Route::group(['prefix' => 'attachment'], function () {
        Route::get('', [AttachmentController::class, 'all']);
        Route::get('/search', [AttachmentController::class, 'findByAllAttributes']);
        Route::get('/file/{path}', [AttachmentController::class, 'showFile'])->where('path', '.*');
        Route::get('/{id}', [AttachmentController::class, 'find'])->whereNumber('id');
        Route::post('', [AttachmentController::class, 'create']);
        Route::get('/{col}/{id}', [AttachmentController::class, 'findByAll'])->where('col', '[A-Za-z_]+');
        Route::put('/{id}', [AttachmentController::class, 'update'])->whereNumber('id');
        Route::delete('/{id}', [AttachmentController::class, 'delete'])->whereNumber('id');
    });

    Route::group(['prefix' => 'ia'], function () {
        Route::get('/documents/upload-completion', [DocumentCompletionController::class, 'uploadCompletion']);
    });

    Route::group(['prefix' => 'document'], function () {
        Route::get('/{col}/{id}', [DocumentController::class, 'findBy']);
    });

    Route::group(['prefix' => 'event'], function () {
        Route::get('', [EventController::class, 'all']);
        Route::get('/{id}', [EventController::class, 'find']);
        Route::post('', [EventController::class, 'create']);
        Route::get('/{col}/{id}', [EventController::class, 'findBy']);
        Route::put('/{id}', [EventController::class, 'update']);
        Route::delete('/{id}', [EventController::class, 'delete']);
    });

    Route::group(['prefix' => 'selector'], function () {
        Route::get('', [SelectorController::class, 'all']);
    });

    Route::group(['prefix' => 'diagnosis/sgsst'], function () {
        Route::get('/companies', [SgsstDiagnosisController::class, 'companiesOverview']);
        Route::get('/companies/{companyId}', [SgsstDiagnosisController::class, 'companyDetail'])->whereNumber('companyId');
        Route::get('/employees/{employeeId}', [SgsstDiagnosisController::class, 'employeeDetail'])->whereNumber('employeeId');
    });
});
