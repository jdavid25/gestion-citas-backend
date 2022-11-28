<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Agenda;
use Illuminate\Http\Request;

use Validator;

class CitaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id_persona)
    {
        if(!auth()){
            return response()
                ->json(['status' => false, 'message' => 'Forbidden'], 403);
        }

        if(auth()->user()->perfil_id == config('constants.medico')){
            $citas = Cita::selectRaw('citas.*, agendas.fecha, agendas.hora, CONCAT(personas.nombre, " ",personas.apellido) as persona, consultorios.nombre as consultorio, consultorios.direccion as direccion')
            ->join('agendas','citas.agenda_id','=','agendas.id')
            ->join('personas','citas.persona_id','=','personas.id')
            ->join('consultorios','agendas.consultorio_id','=','consultorios.id')
            ->where([['citas.estado_id','=',config('constants.activo')],
                    ['agendas.persona_id',$id_persona]])->get();
        }else{
            $citas = Cita::selectRaw('citas.*, agendas.fecha, agendas.hora, CONCAT(personas.nombre, " ",personas.apellido) as persona, consultorios.nombre as consultorio, consultorios.direccion as direccion')
            ->join('agendas','citas.agenda_id','=','agendas.id')
            ->join('personas','agendas.persona_id','=','personas.id')
            ->join('consultorios','agendas.consultorio_id','=','consultorios.id')
            ->where('citas.persona_id',$id_persona)->get();
            //->where([['citas.estado_id','!=',config('constants.eliminado')],
            // ['citas.persona_id',$id_persona]])->get();
        }
        if(count($citas) < 1){
            return response()->json(["status" => false, "msg" => "no hay registros"]);    
        }
        return response()->json(["status" => true, "citas" => $citas]);
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
            'descripcion' => 'required|string|max:255',
            'agenda_id' => 'required',
            'persona_id' => 'required'
        ]);
        
        if($validator->fails()){
            return response()->json(['status' => false, 'message' => $validator->errors()]);
        }

        $agenda = Agenda::find($request->agenda_id);
        
        $cita_pre = Cita::join('agendas','citas.agenda_id','agendas.id')
        ->where([
            ['agendas.fecha',$agenda->fecha],
            ['agendas.hora',$agenda->hora],
            ['agendas.estado_id',"!=",config('constants.eliminado')],
            ['citas.estado_id',"!=",config('constants.eliminado')]
        ])->first();

        if($cita_pre){
            return response()->json(['status' => false, 'show' => true,'message' => 'Ya cuenta con una cita asignada para '.$cita_pre->fecha.' - '.$cita_pre->hora]);
        }

        $cita = Cita::create([
            'descripcion' => $request->descripcion,
            'agenda_id' => $request->agenda_id,
            'persona_id' => $request->persona_id,
            'estado_id' => config('constants.activo')
        ]);

        $agenda = Agenda::find($request->agenda_id);
        $agenda->estado_id = config('constants.bloqueado');
        $agenda->update();

        return response()
            ->json(['status' => true, 'cita' => $cita, 'agenda' => $agenda]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cita  $cita
     * @return \Illuminate\Http\Response
     */
    public function show(Cita $cita)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cita  $cita
     * @return \Illuminate\Http\Response
     */
    public function edit(Cita $cita)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cita  $cita
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cita $cita)
    {
        
        if(!auth()->user()->tokenCan('1')){
            return response()
                ->json(['status' => false, 'message' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(),[
            'descripcion' => 'required|string|max:255',
            'agenda_id' => 'required',
            'persona_id' => 'required'
        ]);
        
        if($validator->fails()){
            return response()->json(['status' => false, 'message' => $validator->errors()]);
        }
        
        $agenda = Agenda::find($request->agenda_id);
        
        $cita_pre = Cita::join('agendas','citas.agenda_id','agendas.id')
        ->where([
            ['agendas.fecha',$agenda->fecha],
            ['agendas.hora',$agenda->hora],
            ['agendas.estado_id',"!=",config('constants.eliminado')],
            ['citas.estado_id',"!=",config('constants.eliminado')]
        ])->first();

        if($cita_pre){
            return response()->json(['status' => false, 'show' => true,'message' => 'Ya cuenta con una cita asignada para '.$cita_pre->fecha.' - '.$cita_pre->hora]);
        }

        $cita = Cita::find($request->id);

        $agenda = Agenda::find($cita->agenda_id);
        $agenda->estado_id = config('constants.disponible');
        $agenda->update();
        
        $agenda = Agenda::find($request->agenda_id);
        $agenda->estado_id = config('constants.bloqueado');
        $agenda->update();

        $cita->update($request->all());

        return response()
            ->json(['status' => true, 'cita' => $cita, 'agenda' => $agenda]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cita  $cita
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        if(!auth()->user()->tokenCan('1')){
            return response()
                ->json(['status' => false, 'message' => 'Forbidden'], 403);
        }
        
        $cita = Cita::find($id);
        if(!$cita){
            return response()->json(["status" => false, "msg" => "No se pudo eliminar!"]);
        }

        $agenda = Agenda::find($cita->agenda_id);
        $agenda->estado_id = config('constants.disponible');
        $agenda->save();

        $cita->estado_id = config('constants.eliminado');
        $cita->save();

        return response()->json(["status" => true, 'cita' => $cita, 'agenda' => $agenda]);
    }

    
    public function cancelarCita(Request $request)
    {
        
        if(!auth()->user()->tokenCan('1')){
            return response()
                ->json(['status' => false, 'message' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(),[
            'id' => 'required',
            'descripcion' => 'required|string'
        ]);
        
        if($validator->fails()){
            return response()->json(['status' => false, 'message' => $validator->errors()]);
        }
        
        $cita = Cita::find($request->id);
        if(!$cita){
            return response()->json(["status" => false, "msg" => "No se pudo cancelar!"]);
        }

        $agenda = Agenda::find($cita->agenda_id);
        $agenda->estado_id = config('constants.disponible');
        $agenda->save();

        $cita->descripcion = $request->descripcion;
        $cita->estado_id = config('constants.eliminado');
        $cita->save();

        return response()->json(["status" => true, 'cita' => $cita, 'agenda' => $agenda]);
    }
}
