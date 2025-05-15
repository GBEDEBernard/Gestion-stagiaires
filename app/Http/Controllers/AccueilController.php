<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccueilController extends Controller
{
    //
    public function acceuil(){
        return view('welcome');
    }

    public function layouts(){
        return view('bloglayouts');
    }

    public function apropo(){
        return view('apropos');
    }
    public function fonction(){
      return view('fonctionalite');
    }
}
