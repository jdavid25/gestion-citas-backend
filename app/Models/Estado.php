<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;
    
    public function agendas(){
        return $this->hasMany('App\Models\Agenda');
    }
    
    public function citas(){
        return $this->hasMany('App\Models\Cita');
    }
    
    public function consultorios(){
        return $this->hasMany('App\Models\Consultorio');
    }
    
    public function diagnosticos(){
        return $this->hasMany('App\Models\Diagnostico');
    }
    
    public function detalle_diagnosticos(){
        return $this->hasMany('App\Models\DetalleDiagnostico');
    }
    
    public function medicamentos(){
        return $this->hasMany('App\Models\Medicamento');
    }
    
    public function municipios(){
        return $this->hasMany('App\Models\Municipio');
    }
    
    public function perfils(){
        return $this->hasMany('App\Models\Perfil');
    }
    
    public function personas(){
        return $this->hasMany('App\Models\Persona');
    }
    
    public function users(){
        return $this->hasMany('App\Models\User');
    }
}
