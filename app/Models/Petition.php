<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Petition extends Model
{
    use HasFactory;
    
    protected $table = 'petitions';


    public function Article()
    {
        return $this->hasOne('App\Models\Article', 'id', 'articles_id');
    }

    public function Status()
    {
        return $this->hasOne('App\Models\PetitionStatus', 'id', 'petition_status_id');
    }

    public function Department()
    {
        return $this->belongsTo('App\Models\Department', 'department_id', 'id');
    }

    public function Area()
    {
        return $this->belongsTo('App\Models\Area', 'area_id', 'id');
    }

    public function History()
    {
        return $this->hasMany('App\Models\PetitionHistory');
    }
}
