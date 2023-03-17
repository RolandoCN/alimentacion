<?php

namespace App\Models\Alimentacion;

use Illuminate\Database\Eloquent\Model;

class GestionMenu extends Model
{
    protected $table = 'al_gestion_menu';
    protected $primaryKey  = 'id_gestion_menu';
    public $timestamps = false;

    public function gestion(){
        return $this->belongsTo('App\Models\Alimentacion\Gestion', 'id_gestion', 'id_gestion');
    }


    public function menu(){
        return $this->belongsTo('App\Models\Alimentacion\Menu', 'id_menu', 'id_menu');
    }

}
?>