<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class CrawlNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:crawler_news
    {--site=easyuni : chọn site để crawl}
    {--collect=post : chọn loại crawl post/content}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $site = $this->option('site');
        $collect = $this->option('collect');

        $limit = 5;

        if ($collect == 'post'){
            $client = new Client();

            $page = 1;
            $host = config("crawler.news.post.$site.domain");
            $end_point = config("crawler.news.post.$site.end_point");
            $news_selector = config("crawler.news.post.$site.news_selector");
            $title_selector = config("crawler.news.post.$site.title_selector");

            while(true){
                try{
                    $response = $client->get($end_point.$page);

                    $data = $response->getBody()->getContents();

                    $crawler = new Crawler();
                    $crawler->addHtmlContent($data);

                    $crawler->filter($news_selector)->each(function(Crawler $node) use ( $host, $site, $title_selector ){
                        $href = $node->attr('href');
                        $url = $this->rel2abs($href, $host);

                        try{
                            $title = $node->filter($title_selector)->getNode(0)->textContent;
                        }catch (\Exception $e){
                            $title = '';
                        }

                        try{

                            \DB::table('news')->insert([
                                'title' => $title,
                                'url' => $url,
                                'crawler' => $site,
                            ]);
                        }catch (\Exception $e){
                            \Log::error($e->getMessage());
                            return;
                        }
                    });

                    $page++;

                }catch (\Exception $e){
                    break;
                }

            }

        }elseif ($collect == 'content'){
            $client = new Client();

            $selector = config("crawler.news.content.$site.selector");

            $news_builer = \DB::table('news')->where('crawler', $site)
                ->where('is_handled', 0);

            while(true){
                $news = $news_builer->take($limit)->get();
                if(count($news) == 0) break;

                foreach ($news as $new){
                    $this->info('Runiing post :'.$new->id);
                    \DB::table('news')->where('id', $new->id)->update(['is_handled' => -1]);

                    $response = $client->get($new->url);

                    $data = $response->getBody()->getContents();

                    $crawler = new Crawler();
                    $crawler->addHtmlContent($data);

                    $content = '';
                    $text = '';

                    $crawler->filter($selector)->each(function(Crawler $node) use(&$content, &$text){
                        $content .= $node->html();
                        $text .= $node->text();
                    });

                    \DB::table('news')->where('id', $new->id)->update(['content' => $content, 'text' => $text, 'is_handled' => 1]);

                }
            }
        }
        else {
            $this->warn('Not support');
        }
    }

    private function rel2abs($rel, $base)
    {
        /* return if already absolute URL */
        if (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;

        /* queries and anchors */
        if ($rel[0]=='#' || $rel[0]=='?') return $base.$rel;

        /* parse base URL and convert to local variables:
           $scheme, $host, $path */
        $parse_url = parse_url($base);
        $scheme = $parse_url['scheme'];
        $host = $parse_url['host'];
        $path = $parse_url['path'];

        /* remove non-directory element from path */
        $path = preg_replace('#/[^/]*$#', '', $path);

        /* destroy path if relative url points to root */
        if ($rel[0] == '/') $path = '';

        /* dirty absolute URL */
        $abs = "$host$path/$rel";

        /* replace '//' or '/./' or '/foo/../' with '/' */
        $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}

        /* absolute URL is ready! */
        return $scheme.'://'.$abs;
    }
}
