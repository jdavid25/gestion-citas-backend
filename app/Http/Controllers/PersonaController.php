<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($perfil)
    {
        if(!auth()->user()->tokenCan('1')){
            return response()
                ->json(['status' => false, 'message' => 'Forbidden'], 403);
        }

        $personas = Persona::select('personas.*','departamentos.id as id_departamento','departamentos.nombre as departamento','municipios.nombre as municipio')
        ->join('users','personas.id','=','users.persona_id')
        ->join('municipios','personas.municipio_id','=','municipios.id')
        ->join('departamentos','municipios.departamento_id','=','departamentos.id')
        ->where([['personas.estado_id',config('constants.activo')],['users.perfil_id',$perfil]])->get();
        if(count($personas) < 1){
            return response()->json(["status" => false, "msg" => "no hay registros"]);    
        }
        return response()->json(["status" => true, "personas" => $personas]);
    }

    public function getPersonaPorId($id){

        $persona = Persona::find($id);

        if(!$persona){
            return response()->json(["status" => false, "msg" => "No se encontró por el ID dado!"]);
        }

        return response()->json(["status" => true, "persona" => $persona]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Persona  $persona
     * @return \Illuminate\Http\Response
     */
    public function show(Persona $persona)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Persona  $persona
     * @return \Illuminate\Http\Response
     */
    public function edit(Persona $persona)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Persona  $persona
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
           
        if(!auth()->user()->tokenCan('1')){
            return response()
                ->json(['status' => false, 'message' => 'Forbidden'], 403);
        }

        $persona = Persona::find($request->id);
        $persona->telefono = $request->telefono;
        $persona->direccion = $request->direccion;
        $persona->update();
        return response()->json(["status" => true, "persona" => $persona]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Persona  $persona
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!auth()->user()->tokenCan('1')){
            return response()
                ->json(['status' => false, 'message' => 'Forbidden'], 403);
        }
        
        $persona = Persona::find($id);
        if(!$persona){
            return response()->json(["status" => false, "msg" => "No se pudo eliminar!"]);
        }
        $persona->estado_id = config('constants.eliminado');
        $persona->save();
        return response()->json(["status" => true, "persona" => $persona]);
    }
}
