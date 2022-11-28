<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\User;
use App\Models\Persona;
use \stdClass;

class AuthController extends Controller
{
    public function register(Request $request){
        if(!auth()->user()->tokenCan('1')){
            return response()
                ->json(['status'=> false, 'message' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(),[
            'tipo_documento' => 'required|string|max:255',
            'num_documento' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:255',
            'municipio_id' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'perfil_id' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json(['status'=> false, 'message' => $validator->errors()]);
        }

        $persona = Persona::create([
            'tipo_documento' => $request->tipo_documento,
            'num_documento' => $request->num_documento,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono,
            'municipio_id' => $request->municipio_id,
            'estado_id' => 1
        ]);

        $user = User::create([
            'name' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->num_documento),
            'persona_id' => $persona->id,
            'perfil_id' => $request->perfil_id,
            'estado_id' => 1
        ]);

        // $token = $user->createToken('auth_token',['1'])->plainTextToken;

        return response()
            ->json(['status'=> true, 'usuario' => $user,'persona' => $persona]);
            //->json(['usuario' => $user,'persona' => $persona, 'access_token' => $token, 'token_type'=> 'Bearer']);
    }

    public function login(Request $request){
        if(!Auth::attempt($request->only('email','password'))){
            return response()
                ->json(['status' => false, 'message' => 'Unauthorized']);
        }

        $user = User::where('email', $request['email'])->firstOrFail();
        $persona = Persona::find($user->persona_id);
        $token = $user->createToken('auth_token',[$user->perfil_id])->plainTextToken;

        return response()
            ->json([
                'status' => true,
                'accessToken' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                'persona' => $persona
            ]);
    }

    public function logout(){
        auth()->user()->tokens()->delete();
        return response()->json(['status' => true, 'message' => 'Se ha cerrado la session']);
    }
}
