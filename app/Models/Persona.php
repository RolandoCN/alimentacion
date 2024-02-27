<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    // protected $connection = 'mysql2';
    protected $table = 'persona';
    protected $primaryKey  = 'idpersona';
    public $timestamps = false;

}
?>