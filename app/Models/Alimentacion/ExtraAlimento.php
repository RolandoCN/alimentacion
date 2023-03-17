<?php

namespace App\Models\Alimentacion;

use Illuminate\Database\Eloquent\Model;

class ExtraAlimento extends Model
{
    protected $table = 'al_alimentos_extra';
    protected $primaryKey  = 'idalimentos_extra';
    public $timestamps = false;

}
?>