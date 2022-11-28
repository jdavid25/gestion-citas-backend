<?php

namespace App\Http\Controllers;

use App\Models\Consultorio;
use Illuminate\Http\Request;

use Validator;

class ConsultorioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->tokenCan('1')){
            return response()
                ->json(['status' => false, 'message' => 'Forbidden'], 403);
        }

        $consultorios = Consultorio::select('consultorios.*','departamentos.id as id_departamento','departamentos.nombre as departamento','municipios.nombre as municipio')
        ->join('municipios','consultorios.municipio_id','=','municipios.id')
        ->join('departamentos','municipios.departamento_id','=','departamentos.id')
        ->where('consultorios.estado_id',config('constants.activo'))->get();
        if(count($consultorios) < 1){
            return response()->json(["status" => false, "msg" => "no hay registros"]);    
        }
        return response()->json(["status" => true, "consultorios" => $consultorios]);
    }

    public function getConsultoriosDepartamento(Request $request)
    {
        if(!auth()->user()->tokenCan('1')){
            return response()
                ->json(['status' => false, 'message' => 'Forbidden'], 403);
        }

        $consultorios = Consultorio::select('consultorios.*','municipios.nombre as municipio')
        ->join('municipios','consultorios.municipio_id','=','municipios.id')
        ->join('departamentos','municipios.departamento_id','=','departamentos.id')
        ->where([['consultorios.estado_id',config('constants.activo')],
                ['departamentos.id',$request->id_departamento]])->get();
        if(count($consultorios) < 1){
            return response()->json(["status" => false, "msg" => "no hay registros"]);    
        }
        return response()->json(["status" => true, "consultorios" => $consultorios]);
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
        if(!auth()->user()->tokenCan('1')){
            return response()
                ->json(['status' => false, 'message' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(),[
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'municipio_id' => 'required|string|max:255'
        ]);
        
        if($validator->fails()){
            return response()->json(['status' => false, 'message' => $validator->errors()]);
        }

        $consultorio = Consultorio::create([
            'nombre' => $request->nombre,
            'direccion' => $request->direccion,
            'municipio_id' => $request->municipio_id,
            'estado_id' => config('constants.activo')
        ]);

        return response()
            ->json(['status' => true, 'consultorio' => $consultorio]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Consultorio  $consultorio
     * @return \Illuminate\Http\Response
     */
    public function show(Consultorio $consultorio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Consultorio  $consultorio
     * @return \Illuminate\Http\Response
     */
    public function edit(Consultorio $consultorio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Consultorio  $consultorio
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Consultorio $consultorio)
    {
        
        if(!auth()->user()->tokenCan('1')){
            return response()
                ->json(['status' => false, 'message' => 'Forbidden'], 403);
        }

        $consultorio = Consultorio::find($request->id);
        $consultorio->update($request->all());
        return response()->json(["status" => true, "consultorio" => $consultorio]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Consultorio  $consultorio
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        if(!auth()->user()->tokenCan('1')){
            return response()
                ->json(['status' => false, 'message' => 'Forbidden'], 403);
        }
        
        $consultorio = Consultorio::find($id);
        if(!$consultorio){
            return response()->json(["status" => false, "msg" => "No se pudo eliminar!"]);
        }
        $consultorio->estado_id = config('constants.eliminado');
        $consultorio->save();
        return response()->json(["status" => true, "consultorio" => $consultorio]);
    }
}
