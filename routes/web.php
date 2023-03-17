<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Alimentacion\GestionController;
use App\Http\Controllers\Alimentacion\MenuController;
use App\Http\Controllers\Alimentacion\GestionMenuController;
use App\Http\Controllers\Alimentacion\PersonaController;
use App\Http\Controllers\Alimentacion\PerfilController;
use App\Http\Controllers\Alimentacion\UsuarioController;
use App\Http\Controllers\Alimentacion\TurnoController;
use App\Http\Controllers\Alimentacion\HorarioAlimentosController;
use App\Http\Controllers\Alimentacion\ListadoTurnoController;
use App\Http\Controllers\Alimentacion\VerificaTurnoController;
use App\Http\Controllers\Alimentacion\ReporteController;
use App\Http\Controllers\Alimentacion\EmpleadoController;
use App\Http\Controllers\Alimentacion\ExtraController;


use App\Http\Controllers\Bodega\IngresoController;

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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/persona', [PersonaController::class, 'index']);

Route::middleware(['auth'])->group(function() { //middleware autenticacion

    //PERSONA
    // Route::get('/persona', [PersonaController::class, 'index'])->middleware('validarRuta');
    Route::get('/listado-persona', [PersonaController::class, 'listar']);
    Route::post('/guardar-persona', [PersonaController::class, 'guardar']);
    Route::get('/editar-persona/{id}', [PersonaController::class, 'editar']);
    Route::put('/actualizar-persona/{id}', [PersonaController::class, 'actualizar']);
    Route::get('/eliminar-persona/{id}', [PersonaController::class, 'eliminar']);


    //PERFILES
    Route::get('/perfil', [PerfilController::class, 'index'])->middleware('validarRuta');
    Route::get('/listado-rol', [PerfilController::class, 'listar']);
    Route::post('/guardar-rol', [PerfilController::class, 'guardar']);
    Route::get('/editar-rol/{id}', [PerfilController::class, 'editar']);
    Route::put('/actualizar-rol/{id}', [PerfilController::class, 'actualizar']);
    Route::get('/eliminar-rol/{id}', [PerfilController::class, 'eliminar']);
    Route::get('/acceso-perfil/{id}', [PerfilController::class, 'accesoPerfil']);
    Route::get('/acceso-por-perfil/{menu}/{tipo}/{perfil}', [PerfilController::class, 'mantenimientoAccesoPerfil']);
    Route::get('/dato-perfil', [PerfilController::class, 'datoPerfil']);



    //GESTION
    Route::get('/gestion', [GestionController::class, 'index'])->middleware('validarRuta');
    Route::get('/listado-gestion', [GestionController::class, 'listar']);
    Route::post('/guardar-gestion', [GestionController::class, 'guardar']);
    Route::get('/editar-gestion/{id}', [GestionController::class, 'editar']);
    Route::put('/actualizar-gestion/{id}', [GestionController::class, 'actualizar']);
    Route::get('/eliminar-gestion/{id}', [GestionController::class, 'eliminar']);

    //MENU
    Route::get('/menu', [MenuController::class, 'index'])->middleware('validarRuta');
    Route::get('/listado-menu', [MenuController::class, 'listar']);
    Route::post('/guardar-menu', [MenuController::class, 'guardar']);
    Route::get('/editar-menu/{id}', [MenuController::class, 'editar']);
    Route::put('/actualizar-menu/{id}', [MenuController::class, 'actualizar']);
    Route::get('/eliminar-menu/{id}', [MenuController::class, 'eliminar']);

    //GESTION-MENU
    Route::get('/gestion-menu', [GestionMenuController::class, 'index'])->middleware('validarRuta');
    Route::get('/listado-gestion-menu', [GestionMenuController::class, 'listar']);
    Route::post('/guardar-gestion-menu', [GestionMenuController::class, 'guardar']);
    Route::get('/editar-gestion-menu/{id}', [GestionMenuController::class, 'editar']);
    Route::put('/actualizar-gestion-menu/{id}', [GestionMenuController::class, 'actualizar']);
    Route::get('/eliminar-gestion-menu/{id}', [GestionMenuController::class, 'eliminar']);


    //USUARIO
    Route::get('/usuario', [UsuarioController::class, 'index'])->middleware('validarRuta');
    Route::get('/listado-usuario', [UsuarioController::class, 'listar']);
    Route::post('/guardar-usuario', [UsuarioController::class, 'guardar']);
    Route::get('/editar-usuario/{id}', [UsuarioController::class, 'editar']);
    Route::put('/actualizar-usuario/{id}', [UsuarioController::class, 'actualizar']);
    Route::get('/eliminar-usuario/{id}', [UsuarioController::class, 'eliminar']);
    Route::post('/cambiar-clave', [UsuarioController::class, 'cambiarClave']);
    Route::get('/resetear-password/{id}', [UsuarioController::class, 'resetearPassword']);

    //GESTION-TURNOS
    Route::get('/consulta-horario', [TurnoController::class, 'index'])->middleware('validarRuta');
    Route::get('/buscar-persona', [TurnoController::class, 'buscarPersona']);
    Route::get('/info-persona/{id}', [TurnoController::class, 'infoPersona']);

    Route::get('fullcalender/{id}', [TurnoController::class, 'mostrar']);
    Route::get('fullcalender_/{id}', [TurnoController::class, 'mostrarAux']);
    Route::post('actualizar-turno-comida', [TurnoController::class, 'actualizarTurnoComida']);
    Route::post('eliminar-turno-comida', [TurnoController::class, 'eliminarTurnoComida']);
    Route::post('/asignar-turno', [TurnoController::class, 'asignar']);


    //GESTION-HORARIO ALIMENTOS
    Route::get('/horario-alimentos', [HorarioAlimentosController::class, 'index'])->middleware('validarRuta');
    Route::get('/listado-horario-alimentos', [HorarioAlimentosController::class, 'listar']);
    Route::post('/guardar-horario', [HorarioAlimentosController::class, 'guardar']);
    Route::get('/editar-horario/{id}', [HorarioAlimentosController::class, 'editar']);
    Route::put('/actualizar-horario/{id}', [HorarioAlimentosController::class, 'actualizar']);
    Route::get('/eliminar-horario/{id}', [HorarioAlimentosController::class, 'eliminar']);
    Route::get('/horario-alimentos/{id}', [HorarioAlimentosController::class, 'alimentosHorario']);
    Route::get('/alimento-por-horario/{alimento}/{tipo}/{horario}', [HorarioAlimentosController::class, 'mantenimientoAlimentoHorario']);


    //GESTION APROBACION DE TURNOS
    Route::get('/listado-turno', [ListadoTurnoController::class, 'index'])->middleware('validarRuta');
    Route::get('/turno-fecha/{fecha}/{alim}', [ListadoTurnoController::class, 'turnosFecha']);
    Route::post('/aprobar-turno', [ListadoTurnoController::class, 'aprobacionTurno']);


    //TURNOS APROBADOS

    Route::get('/turnos-aprobados', [ListadoTurnoController::class, 'vistaAprobado'])->middleware('validarRuta');
    Route::get('/turno-fecha-aprob/{fecha}/{alim}', [ListadoTurnoController::class, 'comidasAprobadas']);
    Route::post('/descargar-aprobacion', [ListadoTurnoController::class, 'descargarAprobacionFechaInd']);
    Route::get('/descargar/{fecha}/{alim}', [ListadoTurnoController::class, 'descargar']);
    Route::get('/descargar-reporte/{nombre}', [ListadoTurnoController::class, 'descargarPdf']);

    //VERIFICACION DE ALIMENTOS FUNCIONARIO

    Route::get('/verificar-alimento', [VerificaTurnoController::class, 'vistaVerifica'])->middleware('validarRuta');
    Route::post('/valida-comida-empleado', [VerificaTurnoController::class, 'validarComida']);


    //REPORTES

    Route::get('/informe-por-usuario', [ReporteController::class, 'informeUsuario'])->middleware('validarRuta');
    Route::get('/alimento-servido-indiv/{f_ini}/{f_fin}/{usuario}', [ReporteController::class, 'alimentoServidoInd']);
    Route::post('/reporte-individual', [ReporteController::class, 'descargarAprobacionFechaInd']);
    Route::get('/test/{f_ini}/{f_fin}/{usuario}', [ReporteController::class, 'testReporte']);

    Route::get('/consolidado-mes', [ReporteController::class, 'informePeriodo'])->middleware('validarRuta');
    Route::get('/alimento-servido-periodo/{f_ini}/{f_fin}', [ReporteController::class, 'alimentoServidoPeriodo']);
    Route::post('/reporte-periodo', [ReporteController::class, 'reportePeriodo']);
    Route::get('/test-fecha/{f_ini}/{f_fin}', [ReporteController::class, 'testReporteFecha']);

    Route::get('/detallado-por-fecha', [ReporteController::class, 'informeDetallado'])->middleware('validarRuta');
    Route::get('/alimento-periodo-detallado/{f_ini}/{f_fin}', [ReporteController::class, 'alimentoServidoDetallado']);
    Route::get('/test-fecha-det/{f_ini}/{f_fin}', [ReporteController::class, 'testReporteFechaDet']);
    Route::post('/reporte-detallado', [ReporteController::class, 'reporteDetallado']);


    //EMPLEADO
    Route::get('/empleado', [EmpleadoController::class, 'index'])->middleware('validarRuta');
    Route::get('/listado-empleado', [EmpleadoController::class, 'listar']);
    Route::post('/guardar-empleado', [EmpleadoController::class, 'guardar']);
    Route::get('/editar-empleado/{id}', [EmpleadoController::class, 'editar']);
    Route::put('/actualizar-empleado/{id}', [EmpleadoController::class, 'actualizar']);
    Route::get('/eliminar-empleado/{id}', [EmpleadoController::class, 'eliminar']);

    //PEDIDO POR MOTIVOS EXTRA
    Route::get('/extra', [ExtraController::class, 'index'])->middleware('validarRuta');
    Route::get('/listado-extra', [ExtraController::class, 'listar']);
    Route::post('/guardar-extra', [ExtraController::class, 'guardar']);
    Route::get('/eliminar-extra/{id}', [ExtraController::class, 'eliminar']);

    //REPORTE POR MOTIVOS EXTRA
    Route::get('/reporte-extra', [ExtraController::class, 'vistaReporte'])->middleware('validarRuta');
    Route::get('/alimento-extra/{fini}/{ffin}', [ExtraController::class, 'alimentoExtraFechas']);
    Route::get('/test-extra/{fini}/{ffin}', [ExtraController::class, 'testreporteExtraFechas']);
    Route::post('/pdf-extra', [ExtraController::class, 'reporteExtraFechas']);



    ///***************INGRESO */

    Route::get('/ingreso', [IngresoController::class, 'index']);
   


});