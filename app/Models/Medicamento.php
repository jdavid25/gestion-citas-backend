<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado_id'
    ];
    
    public function detalle_diagnostico(){
        return $this->hasMany('App\Models\DatealleDiagnostico');
    }
     
    public function estado(){
        return $this->belongsTo('App\Models\Estado');
    }
}
