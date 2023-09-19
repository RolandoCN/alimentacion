<?php

namespace App\Http\Middleware;

use Closure;
use Log;


class ValidaRutaConfirma
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $a)
    {
        dd($request->all());
        $validaRuta=\App\Models\Alimentacion\Empleado::where('cedula',$cedula)
        ->where('pin',$pin)
        ->where('estado','A')->first();

        //si no tiene 
        if(is_null($perfil)){
            // return $idperfil;
            goto NOPERMIRIR;
        }else{
            // return $idperfil;
            goto PERMITIR;
        }
    
        // si no se encuentran coincidencias se redirecciona al login
        NOPERMIRIR:
        // return $rutaLlamada;
        return redirect('/');

        PERMITIR:
      
        return $next($request);
        
    }
}
 