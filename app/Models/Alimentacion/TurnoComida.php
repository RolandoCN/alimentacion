<?php

namespace App\Models\Alimentacion;

use Illuminate\Database\Eloquent\Model;

class TurnoComida extends Model
{
    protected $table = 'al_turno_comida';
    protected $primaryKey  = 'id_turno_comida';
    public $timestamps = false;

    
}
?>