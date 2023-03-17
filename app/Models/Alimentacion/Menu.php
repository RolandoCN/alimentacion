<?php

namespace App\Models\Alimentacion;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'al_menu';
    protected $primaryKey  = 'id_menu';
    public $timestamps = false;

}
?>