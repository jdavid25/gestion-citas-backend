<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    use HasFactory;
    
    public function user(){
        return $this->hasOne('App\Models\User');
    }
     
    public function estado(){
        return $this->belongsTo('App\Models\Estado');
    }
}
