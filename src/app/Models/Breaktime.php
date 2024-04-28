<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Breaktime extends Model
{
    use HasFactory;

    protected $fillable = ['timestamp_id', 'start_time', 'end_time'];

    public function timestamp()
    {
        return $this->belongsTo('App\Models\Timestamp');
    }
}
