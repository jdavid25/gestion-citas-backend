<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;

use Validator;

class AgendaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id_persona)
    {
        if(!auth()->user()->tokenCan('1')){
            return response()
                ->json(['status' => false, 'message' => 'Forbidden'], 403);
        }

        $agendaList = Agenda::select('agendas.*','consultorios.nombre as consultorio','consultorios.direccion as direccion','departamentos.nombre as departamento','municipios.nombre as municipio')
        ->join('consultorios','agendas.consultorio_id','=','consultorios.id')
        ->join('municipios','consultorios.municipio_id','=','municipios.id')
        ->join('departamentos','municipios.departamento_id','=','departamentos.id')
        ->where([['agendas.estado_id','!=',config('constants.eliminado')],
                ['agendas.persona_id',$id_persona]])->get();
        if(count($agendaList) < 1){
            return response()->json(["status" => false, "msg" => "no hay registros"]);    
        }
        return response()->json(["status" => true, "agendaList" => $agendaList]);
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
            'hora_inicio' => 'required|string|max:10',
            'hora_fin' => 'required|string|max:10',
            'fecha' => 'required|string|max:10',
            'consultorio_id' => 'required|string|max:3',
            'persona_id' => 'required'
        ]);
        
        if($validator->fails()){
            return response()->json(['status' => false, 'message' => $validator->errors()]);
        }

        $hora_inicio = explode(':',$request->hora_inicio)[0];
        $hora_fin = explode(':',$request->hora_fin)[0];
        $hora = [];
        if($hora_inicio == $hora_fin || $hora_inicio == ($hora_fin+1)){
            $hora[] = "$hora_inicio:00:00";
        }else{
            for($i = $hora_inicio; $i < $hora_fin; $i++){
                $hora[] = "$i:00:00";
            }
        }

        $ocupado = [];
        $ocupado2 = [];

        foreach($hora as $h){
            $agenda_pre = Agenda::where([
                ['consultorio_id',$request->consultorio_id],
                ['fecha',$request->fecha],
                ['hora',date('H:i:s',strtotime($h))],
                ['estado_id',"!=",config('constants.eliminado')]
            ])->get();

            if(count($agenda_pre) > 0) {
                $ocupado[] = $h;
            }

            $agenda_pre2 = Agenda::where([
                ['persona_id',$request->persona_id],
                ['fecha',$request->fecha],
                ['hora',date('H:i:s',strtotime($h))],
                ['estado_id',"!=",config('constants.eliminado')]
            ])->get();

            if(count($agenda_pre2) > 0) {
                $ocupado2[] = $request->fecha.' '.$h;
            }
        }
        
        if(count($ocupado) > 0){
            return response()->json(['status' => false, 'show' => true,'message' => 'La(s) hora(s) ('.implode(',',$ocupado).') se encuentra(n) ocupada(s)']);
        }
        
        if(count($ocupado2) > 0){
            return response()->json(['status' => false, 'show' => true,'message' => 'Ya cuenta con agenda para '.implode(',',$ocupado2)]);
        }

        foreach($hora as $h){
            $agenda = Agenda::create([
                'fecha' => $request->fecha,
                'hora' => date('H:i:s',strtotime($h)),
                'consultorio_id' => $request->consultorio_id,
                'persona_id' => $request->persona_id,
                'estado_id' => config('constants.disponible')
            ]);
        }

        return response()
            ->json(['status' => true, 'cantidad' => count($hora)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Agenda  $agenda
     * @return \Illuminate\Http\Response
     */
    public function show(Agenda $agenda)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Agenda  $agenda
     * @return \Illuminate\Http\Response
     */
    public function edit(Agenda $agenda)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Agenda  $agenda
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Agenda $agenda)
    {
        
        if(!auth()->user()->tokenCan('1')){
            return response()
                ->json(['status' => false, 'message' => 'Forbidden'], 403);
        }

        $hora = date('H:i:s',strtotime(explode(':',$request->hora_inicio)[0].":00:00"));
        
        $agenda_pre = Agenda::where([
            ['consultorio_id',$request->consultorio_id],
            ['fecha',$request->fecha],
            ['hora', $hora],
            ['estado_id',"!=",config('constants.eliminado')]
        ])->get();

        if(count($agenda_pre) > 0){
            return response()->json(['status' => false, 'show' => true,'message' => 'La hora ('.$hora.') se encuentra ocupada']);
        }

        $agenda = Agenda::find($request->id);
        $agenda->hora = $hora;
        $agenda->fecha = $request->fecha;
        $agenda->consultorio_id = $request->consultorio_id;
        $agenda->update();
        return response()->json(["status" => true, "editar" => "ok"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Agenda  $agenda
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        if(!auth()->user()->tokenCan('1')){
            return response()
                ->json(['status' => false, 'message' => 'Forbidden'], 403);
        }
        
        $agenda = Agenda::find($id);
        if(!$agenda){
            return response()->json(["status" => false, "msg" => "No se pudo eliminar!"]);
        }
        $agenda->estado_id = config('constants.eliminado');
        $agenda->save();
        return response()->json(["status" => true, "agenda" => $agenda]);
    }

    public function agenda_disponible(Request $request)
    {
        if(!auth()->user()->tokenCan('1')){
            return response()
                ->json(['status' => false, 'message' => 'Forbidden'], 403);
        }

        $where = [['agendas.estado_id',config('constants.disponible')],
        ['departamentos.id',$request->id_departamento]];
        if($request->fecha && $request->fecha != ''){
            $where[] = ['agendas.fecha','=',$request->fecha];
        }

        $agendaList = Agenda::selectRaw('agendas.*, consultorios.nombre as consultorio, consultorios.direccion as direccion, departamentos.nombre as departamento, municipios.nombre as municipio, CONCAT(personas.nombre," ",personas.apellido) as persona')
        ->join('personas','agendas.persona_id','=','personas.id')
        ->join('consultorios','agendas.consultorio_id','=','consultorios.id')
        ->join('municipios','consultorios.municipio_id','=','municipios.id')
        ->join('departamentos','municipios.departamento_id','=','departamentos.id')
        ->where($where)->get();
        if(count($agendaList) < 1){
            return response()->json(["status" => false, "msg" => "no hay registros"]);    
        }
        return response()->json(["status" => true, "agendaList" => $agendaList]);
    }
}
