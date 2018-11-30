<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    const NORMAL_TYPE = 0;
    const FEED_TYPE = 1;

    protected $fillable = [
        'url', 'rule', 'type'
    ];

    public function tasks(){
        return $this->hasMany('App\Task');
    }

    public function crawl_histories(){
        return $this->hasMany('App\CrawlHistory');
    }
}
