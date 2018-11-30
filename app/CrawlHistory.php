<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrawlHistory extends Model
{
    protected $fillable = [
        'task_id', 'attempt', 'post_crawled'
    ];

    public function task(){
        return $this->belongsTo('App\Task');
    }

    public function posts(){
        return $this->hasMany('App\Post');
    }
}
