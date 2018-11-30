<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    const READY_STATUS = 0;
    const RUNNING_STATUS = 1;
    const DONE_STATUS = 2;
    const STOP_STATUS = -1;

    protected $fillable = [
        'name', 'site_id', 'status', 'schedule', 're_run'
    ];

    public function site(){
        return $this->belongsTo('App\Site');
    }
}
