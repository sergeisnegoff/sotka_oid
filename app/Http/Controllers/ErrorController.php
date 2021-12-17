<?php

namespace App\Http\Controllers;

class ErrorController
{
    public function error_404(){
        return response()->view('layouts.error.404')->setStatusCode(404);
    }
}
