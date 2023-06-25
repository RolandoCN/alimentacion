<?php

namespace App\Models\Alimentacion;

use Illuminate\Database\Eloquent\Model;

class MenuCabecera extends Model
{
    protected $table = 'al_menu_comida';
    protected $primaryKey  = 'idal_menu_comida';
    public $timestamps = false;

    public function detalle(){
        return $this->hasMany('App\Models\Alimentacion\MenuDetalle', 'idal_menu_comida', 'idal_menu_comida')
        ->where('estado','A');
    }

    public function alimento(){
        return $this->belongsTo('App\Models\Alimentacion\Alimento', 'id_alimento', 'idalimento')
        ->where('estado','A');
    }
   
}
?>