<?php

namespace App\Console\Commands;

use App\Poro_Crawler\DocumentCollector;
use App\Post;
use Illuminate\Console\Command;

class CrawlSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:crawler_sm
    {--site=hoc247 : chọn site để crawl}
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
            $xml_url = config("crawler.sitemap.post.$site.start");
            $selector = config("crawler.sitemap.post.$site.selector");

            $array = json_decode( json_encode(simplexml_load_file($xml_url)), true );


            foreach ($array['url'] as $info){
                $url = $info[$selector];

                try{
                    Post::create([
                        'title' => '',
                        'url' => $url,
                        'crawler' => $site,
                        'crawl_document_id' => null,
                    ]);
                }catch (\Exception $e){
                    \Log::error($e->getMessage());
                    continue;
                }
            }

        }elseif ($collect == 'content'){
            $posts_builer = Post::where('crawler', $site)
                ->where('is_handled', 0);

            while(true){
                $posts = $posts_builer->take($limit)->get();
                if(count($posts) == 0) break;

                foreach ($posts as $post){
                    $this->info('Process post :'.$post->id);
                    $this->info('Running post '.$post->id);
                    $content_crawler = new DocumentCollector($site, $post);
                    $result = $content_crawler->process();
                    if(!$result['success']) {
                        $this->error($result['msg']);
                        continue;
                    }
                }
            }
        }
        else {
            $this->warn('Not support');
        }
    }
}
