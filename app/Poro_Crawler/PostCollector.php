<?php
/**
 * Created by PhpStorm.
 * User: conghoan
 * Date: 7/9/18
 * Time: 10:54
 */

namespace App\Poro_Crawler;


use App\Poro_Crawler\Contract\BasePosterCollector;
use App\Post;
use Openbuildings\Spiderling\Node;

class PostCollector extends BasePosterCollector
{
    protected $crawler_name;

    public function __construct($site , $url = null, $driver = []) {
        $config = config("crawler.sites.post.$site");
        $driver = array_get($config, 'driver', []);
        parent::__construct($config, $url, $driver);

        $this->crawler_name = $site;
    }

    public function process($fail_status = -1)
    {
        $result = [
            'success' => false,
            'msg' => ''
        ];

        try{
            $link = $this->crawl_document->url;

            $this->page->visit($link);

            $this->crawl(0);
            $this->crawl_document->crawl_status	= 1;
            $this->crawl_document->save();

            $result['success'] = true;
            return $result;
        }catch (\Exception $e){
            $this->crawl_document->crawl_status	= -1;
            $this->crawl_document->save();
            $result['msg'] = $e->getMessage();
            return $result;
        }
    }

    public function crawl($rule_index = 0){
        try{
            $rule = array_get($this->config['rule'], $rule_index, null);
            if(!$rule){
                //thêm link mới vào dtb
                return;
            }

            $type = $rule['type'];

            switch ($type){
                case 'links':
                    $selector = $rule['selector'];
                    if(!$selector) return;

                    $els = $this->els($selector);
                    if(count($els) == 0) \Log::error("$rule_index : ".$this->page->current_url());

                    foreach($els as $el){
                        $text = $el->text();
                        $url = $this->convertLink($el->attribute('href'));

                        Post::create([
                            'title' => $text,
                            'url' => $url,
                            'crawler' => $this->crawler_name,
                            'crawl_document_id' => $this->crawl_document->id,
                        ]);
                    }

                    break;
                case 'click':
                    $selector = $rule['selector'];
                    if(!$selector) return;

                    $els = $this->els($selector);

                    if(count($els) == 0) \Log::error("$rule_index : ".$this->page->current_url());

                    foreach($els as $k => $el){
                        /** @var Node $el */
                        try{
                            $old_link = $this->page->current_url();

                            $text = $el->text();
                            $href = $el->attribute('href');

                            if(!$href) continue;
                            $url = $this->getClickLink($el, $this->page->current_url());

                            dump($text, $url);
                            sleep(2);

                            if($old_link != $url){
                                Post::create([
                                    'title' => $text,
                                    'url' => $url,
                                    'crawler' => $this->crawler_name,
                                    'crawl_document_id' => $this->crawl_document->id,
                                ]);
                            }

                        }catch (\Exception $e){
                            \Log::error($e->getMessage());
                        }
                    }
                    dd('done');
                    break;
                case 'other':
                    $parent_selector = array_get($rule, 'parent_selector', null);

                    try{
                        $children = $this->els($parent_selector." > *");
                    }catch (\Exception $e){
                        \Log::error("Can not get start element!");
                        break;
                    }
                    if(count($children) == 0) \Log::error("$rule_index : ".$this->page->current_url());

                    $is_content_page = true;

                    $ul_els = [];

                    foreach ($children as $k => $child){
                        /** @var  Node $child */
                        $text = trim($child->text());

                        if(preg_match('/khác\s*:$/', $text)) break;

                        $tag_name = $child->tag_name();
                        $class = $child->attribute('class');

                        if($tag_name == 'ul' && $class == 'list'){
                            $is_content_page = false;

                            $ul_els[] = $child;
                        }
                    }

                    if($is_content_page){
                        $h1_ele = $this->el($parent_selector.' h1');
                        $title = ($h1_ele) ? $h1_ele->text() : '';

                        try{
                            Post::create([
                                'title' => $title,
                                'url' => $this->page->current_url(),
                                'crawler' => $this->crawler_name,
                                'crawl_document_id' => $this->crawl_document->id,
                            ]);
                        }catch (\Exception $e){
                            \Log::error($e->getMessage());

                        }

                    }else{
                        foreach ($ul_els as $k => $child){
                            $text = $child->text();
                            $url = $child->attribute('href');

                            $url = $this->convertLink($url);
                            try{
                                Post::create([
                                    'title' => $text,
                                    'url' => $url,
                                    'crawler' => $this->crawler_name,
                                    'crawl_document_id' => $this->crawl_document->id,
                                ]);
                            }catch (\Exception $e){
                                \Log::error($e->getMessage());

                            }
                        }
                    }

                    break;
                default:
                    dd($rule_index, 'stop');
            }
        }catch (\Exception $e){
            \Log::error('GetLinkPost: ' . $e->getMessage());
            return [];
        }
    }

    private function getClickLink(Node $element, $old_link){
        $element->click();

        sleep(2);

        $link = $this->page->current_url();

        $this->page->visit($old_link);

        sleep(2);

        return $link;

    }
}