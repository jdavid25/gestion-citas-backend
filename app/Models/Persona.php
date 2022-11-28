<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_documento',
        'num_documento',
        'nombre',
        'apellido',
        'direccion',
        'telefono',
        'email',
        'municipio_id',
        'estado_id'
    ];

    public function user(){
        return $this->hasOne('App\Models\User');
    }

    public function agendas(){
        return $this->hasMany('App\Models\Agenda');
    }

    public function citas(){
        return $this->hasMany('App\Models\Cita');
    }    
     
    public function municipio(){
        return $this->belongsTo('App\Models\Municipio');
    }

    public function estado(){
        return $this->belongsTo('App\Models\Estado');
    }
}
