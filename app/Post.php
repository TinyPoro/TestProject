<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title', 'content', 'crawl_history_id', 'site_id'
    ];

    public function crawl_history(){
        return $this->belongsTo('App\CrawlHistory');
    }

    public function site(){
        return $this>$this->belongsTo('App\Site');
    }
}
