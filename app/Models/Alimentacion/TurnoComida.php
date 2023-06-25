<?php

namespace App\Models\Alimentacion;

use Illuminate\Database\Eloquent\Model;

class TurnoComida extends Model
{
    protected $table = 'al_turno_comida';
    protected $primaryKey  = 'id_turno_comida';
    public $timestamps = false;

    public function usuario_aprueba(){
        return $this->belongsTo('App\Models\User', 'id_usuario_aprueba', 'id')->with('persona');
    }
    
}
?>