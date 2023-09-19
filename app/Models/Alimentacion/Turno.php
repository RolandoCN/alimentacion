<?php

namespace App\Models\Alimentacion;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $table = 'al_turno';
    protected $primaryKey  = 'id';
    public $timestamps = false;

    public function horarioAlimento(){
        return $this->belongsTo('App\Models\Alimentacion\HorarioAlimento', 'id_horario', 'id_horario');
    }

    public function empleado(){
        return $this->belongsTo('App\Models\Alimentacion\Empleado', 'id_persona', 'id_empleado')
        ->with('puesto','area');
    }

    public function usuario_aprueba(){
        return $this->belongsTo('App\Models\User', 'id_usuario_act', 'id')->with('persona');
    }

}
?>