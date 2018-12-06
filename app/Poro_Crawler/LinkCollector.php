<?php
/**
 * Created by PhpStorm.
 * User: conghoan
 * Date: 7/9/18
 * Time: 10:46
 */

namespace App\Poro_Crawler;


use App\Poro_Crawler\Contract\LinkCrawler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Openbuildings\Spiderling\Node;

class LinkCollector extends LinkCrawler
{
    protected $crawler_name;

    protected $client;

    public function __construct($site , $url = null, $driver = []) {
        $config = config("crawler.sites.link.$site");
        $driver = array_get($config, 'driver', []);
        parent::__construct($config, $url, $driver);

        $this->crawler_name = $site;
        $this->client = new Client();
    }

    public function process($fail_status = -1)
    {
        try{
            $this->page->visit($this->url);
            $this->crawl([], 0);
        }catch (\Exception $e){
            \Log::error($e->getMessage());
        }
    }

    public function crawl($data = [], $rule_index = 0, Node $current_node = null){
        try{
            $rule = array_get($this->config['rule'], $rule_index, null);
            if(!$rule){
                //thêm link mới vào dtb
                try{
                    $text_node = $current_node->tag_name() == 'a' ? $current_node : $current_node->find('a');
                    $url = $this->convertLink($text_node->attribute('href'));
                    $text = $text_node->text();
                }catch (\Exception $e){
                    \Log::error($e->getMessage());
                    return;
                }

                dump("Add url $url");

                $this->get_crawl_document([
                    'title' => $text,
                    'url' => $url,
                    'category_data' => json_encode( $data ),
                    'category_id' => 0,
                ], true);

                return;
            }

            $type = $rule['type'];

            switch ($type){
                case 'link':
                    $els = $this->link($rule);

                    $exclude_texts = array_get($rule,'exclude_texts', []);

                    if(count($els) == 0) \Log::error("$rule_index : ".$this->page->current_url());

                    foreach($els as $el){
                        $text = $el['text'];
                        $url = $el['url'];

                        if(in_array($text, $exclude_texts)) continue;

                        $this->page->visit($url);

                        $new_data = array_merge($data, [$text]);
                        $this->crawl($new_data, $rule_index + 1);
                    }

                    break;
                case 'loop':

                    if($current_node) {
                        $selector = $rule['loop_selector'];
                        $text_selectors = $rule['text_selector'];

                        $node = $current_node;
                    }
                    else {
                        $selector = $rule['start_selector'];
                        $text_selectors = $rule['start_text_selector'];

                        $node = $this->page;
                    }

                    $els = $this->all( $node, $selector, 'xpath' );
                    if(count($els) == 0) \Log::error("$rule_index : ".$this->page->current_url());

                    if(count($els) == 0) {
                        $this->crawl($data, $rule_index + 1, $current_node);
                        return;
                    }

                    foreach($els as $k => $el){
                        $text_node = null;

                        foreach ( $text_selectors as $text_selector ){
                            $text_node = $this->find( $el, $text_selector, 'xpath' );

                            if( $text_node ) break;
                        }

                        if( !$text_node ) continue;

                        $text = $text_node->text();

                        $new_data = array_merge($data, [$text]);

                        $this->crawl($new_data, $rule_index, $el);
                    }

                    break;
                case 'get_link':
                    $item_selector = array_get($rule, 'item_selector', null);

                    if(!$item_selector) break;

                    try{
                        $children = $this->els($item_selector);
                    }catch (\Exception $e){
                        \Log::error("Can not get start element!");
                        break;
                    }

                    if(count($children) == 0) \Log::error("$rule_index : ".$this->page->current_url());

                    foreach ($children as $k => $child){
                        $text = $child->text();

                        if(mb_stripos($text, 'mục lục') !== false) continue;

                        $new_data = array_merge($data, [$text]);

                        $this->crawl($new_data, $rule_index + 1, $child);
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

    public function all( Node $node, $selector, $type = 'css'){

        try{

            $els = $node->all( [$type, $selector] );

        } catch ( \Exception $e ){

            $els = [];

        }

        return $els;
    }

    public function find( Node $node, $selector, $type = 'css'){

        try{

            $el = $node->find( [$type, $selector] );

        } catch ( \Exception $e ){

            $el = null;

        }

        return $el;
    }

    public function isValidTag($tag_name){
        if(preg_match('/^h[2-6]$/', $tag_name)) return true;
        if($tag_name == 'ul') return true;

        return false;
    }

    public function parentUntil(\App\Poro_Crawler\Entity\Node $node, $level){
        if($node->getLevel() < $level) return $node;
        if($node->isRoot()) return $node;

        return $this->parentUntil($node->getParent(), $level);
    }

    public function link($rule){
        $selector = $rule['selector'];
        if(!$selector) return [];

        $els = $this->els($selector);

        $els = array_map(function($el){
            /** @var $el \Openbuildings\Spiderling\Node */
            return [
                'text' => $el->text(),
                'url' => $this->convertLink($el->attribute('href'))
            ];
        }, $els->as_array());

        return $els;
    }

    public function detectCategory($data){
        try {
            $main_server = config('crawler.main_server');
            $api_token = config('crawler.api_token');

            $response = $this->client->request('POST', $main_server.'/category/find_category', [
                'headers' => [
                    'Authorization' => "Bearer $api_token"
                ],
                'form_params' => [
                    'data' => $data,
                ]
            ]);

            $res = json_decode(trim($response->getBody()->getContents()));

            return $res->response->data;
        } catch (GuzzleException $e) {
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function init_stack()
    {
        // TODO: Implement init_stack() method.
    }

    public function crawler_name()
    {
        return $this->crawler_name;
    }

    public function save_state()
    {
        // TODO: Implement save_state() method.
    }

    public function resume()
    {
        // TODO: Implement resume() method.
    }

    public function clear_state_cached()
    {
        // TODO: Implement clear_state_cached() method.
    }

    public function monitor()
    {
        // TODO: Implement monitor() method.
    }
}