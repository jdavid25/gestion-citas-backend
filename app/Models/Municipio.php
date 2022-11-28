<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;
    
    public function departamento(){
        return $this->belongsTo('App\Models\Departamento');
    }
     
    public function estado(){
        return $this->belongsTo('App\Models\Estado');
    }
}
