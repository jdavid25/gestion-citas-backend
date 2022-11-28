<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'descripcion',
        'cita_id',
        'estado_id'
    ];
    
    public function datalle_diagnosticos(){
        return $this->hasMany('App\Models\DetalleDiagnostico');
    }

    public function cita(){
        return $this->belongsTo('App\Models\Cita');
    }
    
    public function estado(){
        return $this->belongsTo('App\Models\Estado');
    }

}
