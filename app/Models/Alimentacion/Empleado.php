<?php

namespace App\Models\Alimentacion;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'empleado';
    protected $primaryKey  = 'id_empleado';
    public $timestamps = false;

    public function puesto(){
        return $this->belongsTo('App\Models\Alimentacion\Puesto', 'id_puesto', 'id_puesto')
        ->where('estado','A');
    }

    public function area(){
        return $this->belongsTo('App\Models\Alimentacion\Area', 'id_area', 'id_area')
        ->where('estado','A');
    }

}
?>