<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //
    public function acceuil(){
        return view('welcome');
    }
}
