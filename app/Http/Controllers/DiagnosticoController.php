<?php

namespace App\Http\Controllers;

use App\Models\Diagnostico;
use App\Models\DiagnosticoDetalle;
use App\Models\Cita;
use App\Models\Medicamento;
use Illuminate\Http\Request;

use Validator;

class DiagnosticoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->tokenCan('3')){
            return response()
                ->json(['status' => false, 'message' => 'Forbidden'], 403);
        }

        $diagnostico = Diagnostico::selectRaw('diagnosticos.*, agendas.fecha, agendas.hora, CONCAT(personas.nombre, " ",personas.apellido) as persona, consultorios.nombre as consultorio')
            ->join('citas','diagnosticos.cita_id','citas.id')
            ->join('agendas','citas.agenda_id','agendas.id')
            ->join('consultorios','agendas.consultorio_id','consultorios.id')
            ->join('personas','agendas.persona_id','personas.id')
            ->where([['diagnosticos.cita_id',$request->id_cita],
                ['citas.estado_id',config('constants.finalizado')]])
            ->first();

        $diagnostico_detalle = DiagnosticoDetalle::select('diagnostico_detalles.*','medicamentos.nombre')
            ->join('medicamentos','diagnostico_detalles.medicamento_id','medicamentos.id')
            ->where([['diagnostico_id',$diagnostico->id],
                ['diagnostico_detalles.estado_id',config('constants.activo')]])->get();

        return response()->json(['status' => true, 'diagnostico' => $diagnostico, 'medicamentos' => $diagnostico_detalle]);
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
        if(!auth()->user()->tokenCan('2')){
            return response()
                ->json(['status' => false, 'message' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(),[
            'descripcion' => 'required|string|max:255',
            'cita_id' => 'required'
        ]);
        
        if($validator->fails()){
            return response()->json(['status' => false, 'message' => $validator->errors()]);
        }

        // return response()->json(['status' => false, 'message' => $request->detalle]);

        $diagnostico = Diagnostico::create([
            'descripcion' => $request->descripcion,
            'cita_id' => $request->cita_id,
            'estado_id' => config('constants.activo')
        ]);

        if(is_array($request->detalle)){
            foreach($request->detalle as $d){
                $diagnostico_detalle = DiagnosticoDetalle::create([
                    'descripcion' => $d['descripcion'],
                    'diagnostico_id' => $diagnostico->id,
                    'medicamento_id' => $d['id_medicamento'],
                    'cantidad' => $d['cantidad'],
                    'estado_id' => config('constants.activo')
                ]);
            }
        }
        
        $cita = Cita::find($request->cita_id);
        $cita->estado_id = config('constants.finalizado');
        $cita->save();

        return response()->json(["status" => true, "diagnostico" => $diagnostico, "cita" => $cita]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Diagnostico  $diagnostico
     * @return \Illuminate\Http\Response
     */
    public function show(Diagnostico $diagnostico)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Diagnostico  $diagnostico
     * @return \Illuminate\Http\Response
     */
    public function edit(Diagnostico $diagnostico)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Diagnostico  $diagnostico
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Diagnostico $diagnostico)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Diagnostico  $diagnostico
     * @return \Illuminate\Http\Response
     */
    public function destroy(Diagnostico $diagnostico)
    {
        //
    }
}
