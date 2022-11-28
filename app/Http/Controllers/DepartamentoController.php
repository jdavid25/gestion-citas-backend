<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departamento;

class DepartamentoController extends Controller
{
    //
    public function index(){
        if(!auth()->user()->tokenCan('1')){
            return response()
                ->json(['status'=> false, 'message' => 'Forbidden']);
        }

        return response()
            ->json(['status' => true,'departamentos' => Departamento::all()]);
    }
}
