<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'descripcion',
        'persona_id',
        'agenda_id',
        'estado_id'
    ];
 
    public function diagnostico(){
        return $this->hasOne('App\Models\Diagnostico');
    }

    public function persona(){
        return $this->belongsTo('App\Models\Persona');
    }

    public function agenda(){
        return $this->belongsTo('App\Models\Agenda');
    }

    public function estado(){
        return $this->belongsTo('App\Models\Estado');
    }
}
