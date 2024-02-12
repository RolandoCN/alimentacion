<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use \Log;
use DB;
use Illuminate\Http\Request;

class ApiController extends Controller
{
      
    public function index(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');
        dd("ssas");
    }
}