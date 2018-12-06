<?php

namespace App\Models;

use App\Category;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CrawlDocument
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $language
 * @property string $country
 * @property string $category
 * @property string $tags
 * @property string $crawler
 * @property string $url
 * @property string $file_disk
 * @property string $file_name
 * @property string $file_list
 * @property boolean $is_imported
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CrawlDocument whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CrawlDocument whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CrawlDocument whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CrawlDocument whereLanguage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CrawlDocument whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CrawlDocument whereCategory($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CrawlDocument whereTags($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CrawlDocument whereCrawler($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CrawlDocument whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CrawlDocument whereFileDisk($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CrawlDocument whereFileName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CrawlDocument whereFileList($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CrawlDocument whereIsImported($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CrawlDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CrawlDocument whereUpdatedAt($value)
 */
class CrawlDocument extends Model
{
    //
	protected $table = 'crawl_documents';

    public function getCategoryAttribute(){
        $cate_id = $this->category_id;

        $cate = Category::find($cate_id);

        if($cate) return $cate->name;
        return 'Error';
    }

    public function posts(){
        return $this->hasMany('App\Post');
    }
}
