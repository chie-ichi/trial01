<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timestamp extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'status', 'start_time', 'end_time', 'break_duration', 'work_duration'];
    protected $dates = ['start_time', 'end_time', 'break_duration', 'work_duration'];

    public function breaktimes()
    {
        return $this->hasMany('App\Models\Breaktime');
    }
}
