<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetitionHistory extends Model
{
    use HasFactory;

    protected $table = 'petitions_history';

    public function User()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function PrevStatus()
    {
        return $this->belongsTo('App\Models\PetitionStatus', 'previous_status_id', 'id');
    }

    public function NextStatus()
    {
        return $this->belongsTo('App\Models\PetitionStatus', 'next_status_id', 'id');
    }
}
