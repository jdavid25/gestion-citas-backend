<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'fecha',
        'hora',
        'persona_id',
        'consultorio_id',
        'estado_id'
    ];
    
    public function cita(){
        return $this->hasOne('App\Models\Cita');
    }

    public function persona(){
        return $this->belongsTo('App\Models\Persona');
    }

    public function consultorio(){
        return $this->belongsTo('App\Models\Consultorio');
    }

    public function estado(){
        return $this->belongsTo('App\Models\Estado');
    }
}
