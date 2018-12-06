<?php
/**
 * Created by PhpStorm.
 * User: conghoan
 * Date: 7/9/18
 * Time: 10:54
 */

namespace App\Poro_Crawler;


use App\Crawler\Loigiaihay\SeparateContent;
use App\Poro_Crawler\Contract\BaseDocumentCollector;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Openbuildings\Spiderling\Node;

class DocumentCollector extends BaseDocumentCollector
{
    protected $crawler_name;
    protected $client;

    public function __construct($site , $url = null, $driver = []) {
        $config = config("crawler.sites.content.$site");
        $driver = array_get($config, 'driver', []);
        parent::__construct($config, $url, $driver);

        $this->crawler_name = $site;
        $this->client = new Client();
    }

    public function process($fail_status = -1)
    {
        $result = [
            'success' => false,
            'msg' => ''
        ];

        try{
            $link = $this->post->url;
            $this->page->visit($link);

            $this->crawl(0);
            if($this->post->content) $this->post->is_handled = 1;
            else $this->post->is_handled = -1;

            $this->post->save();

            $result['success'] = true;
            return $result;
        }catch (\Exception $e){
            $this->post->is_handled	= -1;
            $this->post->save();
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
                case 'html':
                    $html = '';
                    $text = '';
                    $selector = $rule['selector'];
                    $valid_selector = array_get($rule, 'valid_selector', null);

                    $els = $this->els($selector);
                    if(count($els) == 0){
                        \Log::error('Wrong select at '.$this->post->url);
                    }

                    foreach($els as $el){
                        /** @var $el Node */
                        if( !$valid_selector || in_array( $el->tag_name(), $valid_selector ) ) {
                            $content = $el->html();

//                            if(!preg_match("/$this->crawler_name/ui", $content)) {
                                $html .= $content;
                                $text .= $el->text().' ';
//                            }
                        }
                    }

                    $split_html = $this->split($html);

                    $this->post->subject = $split_html[0];
                    $this->post->content = $split_html[1];

                    $split_text = $this->split($text);

                    $this->post->subject_text = $split_text[0];
                    $this->post->text = $split_text[1];

                    $this->post->save();

                    break;
                case 'other':
                    $parent_selector = array_get($rule, 'parent_selector', null);

                    try{
                        $children = $this->els($parent_selector." > *");
                    }catch (\Exception $e){
                        \Log::error("Can not get element!");
                        break;
                    }

                    $valid_tags = ['p', 'img', 'table'];

                    $html = '';
                    $text = '';

                    foreach ($children as $k => $child){
                        /** @var  Node $child */
                        $text = trim($child->text());

                        if(preg_match('/khác\s*:$/', $text)) break;

                        $tag_name = $child->tag_name();

                        if(in_array($tag_name, $valid_tags)) {
                            $html .= $child->html();
                            $text .= $child->text().' ';
                        }
                    }

                    $split_html = $this->split($html);

                    $this->post->subject = $split_html[0];
                    $this->post->content = $split_html[1];

                    $split_text = $this->split($text);

                    $this->post->subject_text = $split_text[0];
                    $this->post->text = $split_text[1];

                    $this->post->save();

                    break;
                default:
                    dd($rule_index, 'stop');
            }
        }catch (\Exception $e){
            \Log::error('GetLinkPost: ' . $e->getMessage());
            return [];
        }
    }

    private function split($text){
        $regex_split = "/(Lời giải chi tiết|Lời giải|Hướng dẫn giải|Giải|GỢI Ý LÀM BÀI|Trả lời|Phương pháp giải - Xem chi tiết|Giải|Gỉải|Hướng dẫn giải|Đáp án chi tiết|Đáp án|BÀI THAM KHẢO|Bài Tham Khảo|Hướng dẫn trả lời)/";
        $arr_content = preg_split($regex_split,$text);

        if(count($arr_content) >= 2){
            $first_part = array_get($arr_content,0,'');
            $second_part = str_replace($first_part, '', $text);

            return [$first_part, $second_part];
        }else{
            return ['', $text];
        }

    }

    public function handleHtml($html){

        if(preg_match_all('/src="(.+?)"/', $html, $matches)){
            $urls = array_get($matches, 1, []);
            if(!$urls) return $html;

            foreach($urls as $url){
                $download_path = $this->downloadMedia($url);
                $html = str_replace($url, $download_path, $html);
            }
        }

        return $html;
    }

    public function downloadMedia($url){
        try {
            $main_server = config('crawler.main_server');
            $api_token = config('crawler.api_token');

            $response = $this->client->request('POST', $main_server.'/multipart', [
                'headers' => [
                    'Authorization' => "Bearer $api_token"
                ],
                'form_params' => [
                    'url' => $url,
                ]
            ]);

            sleep(10);

            $res = json_decode(trim($response->getBody()->getContents()));
            return $res->response->data;
        } catch (GuzzleException $e) {
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}