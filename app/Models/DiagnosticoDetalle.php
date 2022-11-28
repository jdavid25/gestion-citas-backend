<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosticoDetalle extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'cantidad',
        'descripcion',
        'diagnostico_id',
        'medicamento_id',
        'estado_id'
    ];
     
    public function diagnostico(){
        return $this->belongsTo('App\Models\Diagnostico');
    }

    public function medicamento(){
        return $this->belongsTo('App\Models\Medicamento');
    }
     
    public function estado(){
        return $this->belongsTo('App\Models\Estado');
    }
}
