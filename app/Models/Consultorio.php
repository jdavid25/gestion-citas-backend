<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultorio extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nombre',
        'direccion',
        'municipio_id',
        'estado_id'
    ];
     
    public function agendas(){
        return $this->hasMany('App\Models\Agenda');
    }

    public function municipio(){
        return $this->belongsTo('App\Models\Municipio');
    }

    public function estado(){
        return $this->belongsTo('App\Models\Estado');
    }
}
