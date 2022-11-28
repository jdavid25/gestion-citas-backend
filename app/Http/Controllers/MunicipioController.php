<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Municipio;

class MunicipioController extends Controller
{
    //
    public function index(){
        if(!auth()->user()->tokenCan('1')){
            return response()
                ->json(['status'=> false, 'message' => 'Forbidden']);
        }

        $municipios = Municipio::where('estado_id', config('constants.activo'))->get();
        return response()
            ->json(['status' => true,'municipios' => $municipios]);
    }

    public function getPorDepartamento($id){
        if(!auth()->user()->tokenCan('1')){
            return response()
                ->json(['status'=> false, 'message' => 'Forbidden']);
        }
        
        $municipios = Municipio::where('departamento_id',$id)->get();
        return response()->json(['status' => true, 'municipios' => $municipios]);
    }
}
