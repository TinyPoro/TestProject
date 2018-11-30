<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    const NORMAL_TYPE = 0;
    const FEED_TYPE = 1;

    protected $fillable = [
        'url', 'rule', 'type', 'category_id'
    ];

    public function tasks(){
        return $this->hasMany('App\Task');
    }

    public function crawl_histories(){
        return $this->hasMany('App\CrawlHistory');
    }

    public function category(){
        return $this>$this->belongsTo('App\Category');
    }

    public function posts(){
        return $this->hasMany('App\Post');
    }
}
