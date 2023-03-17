<?php

namespace App\Models\Alimentacion;

use Illuminate\Database\Eloquent\Model;

class Alimento extends Model
{
    protected $table = 'alimento';
    protected $primaryKey  = 'idalimento';
    public $timestamps = false;

}
?>