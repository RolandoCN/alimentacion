<?php

namespace App\Models\Alimentacion;

use Illuminate\Database\Eloquent\Model;

class PerfilAcceso extends Model
{
    protected $table = 'al_perfil_acceso';
    protected $primaryKey  = 'id_perfil_acceso';
    public $timestamps = false;

    public function menu(){
        return $this->belongsTo('App\Models\Alimentacion\Menu', 'id_menu', 'id_menu');
    }
}
?>