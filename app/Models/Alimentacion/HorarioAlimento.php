<?php

namespace App\Models\Alimentacion;

use Illuminate\Database\Eloquent\Model;

class HorarioAlimento extends Model
{
    protected $table = 'horario_alimento';
    protected $primaryKey  = 'idhorario_alimento';
    public $timestamps = false;

    public function alimento(){
        return $this->belongsTo('App\Models\Alimentacion\Alimento', 'idalimento', 'idalimento')
        ->where('estado','A');
    }

    public function horario(){
        return $this->belongsTo('App\Models\Alimentacion\Horario', 'id_horario', 'id_horario')
        ->where('estado','A');
    }

    public function turno(){
        return $this->belongsTo('App\Models\Alimentacion\Turno', 'id_horario', 'id_horario')
        ->where('estado','!=','E')->with('empleado');
    }
}
?>