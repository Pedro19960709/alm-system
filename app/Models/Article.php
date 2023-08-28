<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'articles';

    public function MeasurementUnit()
    {
        return $this->hasOne('App\Models\MeasurementUnit', 'id', 'measurement_units_id');
    }

}
