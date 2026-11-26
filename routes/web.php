<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\{
    HomeController, SobreController, AuthController, PerfilController,
    ServicosController, ProcessoController, SolucaoController,
    ConsultaController, GoogleCalendarController
};

// --- ROTAS PÚBLICAS ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sobre', [SobreController::class, 'index'])->name('sobre');
Route::get('/servicos', [ServicosController::class, 'index'])->name('servicos');
Route::get('/solucoes', [SolucaoController::class, 'index'])->name('solucoes');
Route::get('/recursos', fn() => view('recursos'))->name('recursos');
Route::get('/contato', fn() => view('contato'))->name('contato');

// Autenticação
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- ROTAS PROTEGIDAS (EXIGEM LOGIN) ---
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn() => view('auth.dashboard'))->name('dashboard');
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil');

    // Processos (Agora totalmente protegido)
    Route::post('processos/da-consulta', [ProcessoController::class, 'createFromConsulta'])
        ->name('processos.createFromConsulta');
    Route::resource('processos', ProcessoController::class);

    // Consulta e Agenda (Recomendado deixar aqui se forem uso interno do advogado)
    Route::get('/consulta', [ConsultaController::class, 'index'])->name('consulta.index');
    Route::post('/consulta', [ConsultaController::class, 'buscar'])->name('consulta.buscar');

    Route::prefix('agenda-google')->name('google-calendar.')->group(function () {
        Route::get('/', [GoogleCalendarController::class, 'index'])->name('index');
        Route::get('/eventos', [GoogleCalendarController::class, 'events'])->name('events');
    });
});
