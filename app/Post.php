<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title', 'url', 'crawler', 'content', 'crawl_document_id',
    ];

    public function crawl_document(){
        return $this->belongsTo('App\Models\CrawlDocument');
    }
}
