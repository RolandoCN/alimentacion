<?php

namespace App\Models\Alimentacion;

use Illuminate\Database\Eloquent\Model;

class AlimentoPaciente extends Model
{
    protected $table = 'al_alimentos_pacientes';
    protected $primaryKey  = 'idal_alimentos_pacientes';
    public $timestamps = false;

}
?>