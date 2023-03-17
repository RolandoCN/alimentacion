<?php

namespace App\Models\Alimentacion;

use Illuminate\Database\Eloquent\Model;

class UsuarioPerfil extends Model
{
    protected $table = 'al_perfil_usuario';
    protected $primaryKey  = 'idperfil_usuario';
    public $timestamps = false;

    public function nombre_perfil(){
        return $this->belongsTo('App\Models\Alimentacion\Perfil', 'id_perfil', 'id_perfil')->where('estado', 'A');
    }
}
?>