<?php

namespace App\Console\Commands;

use App\Models\CrawlDocument;
use App\Poro_Crawler\DocumentCollector;
use App\Poro_Crawler\LinkCollector;
use App\Poro_Crawler\PostCollector;
use App\Post;
use Illuminate\Console\Command;

class RunCrawler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:crawler
    {--site=loigiaihay : chọn site để crawl}
    {--collect=link : chọn loại crawl link/post/content}';

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

        if ($collect == 'link'){
            $document_link_collector = new LinkCollector($site);
            $document_link_collector->process();
        }elseif ($collect == 'post'){
            $links_builder = CrawlDocument::where('crawler', $site)
                ->where('crawl_status', 0);

            while(true){
                $links = $links_builder->take($limit)->get();
                if(count($links) == 0) break;

                foreach ($links as $link){
                    $this->info('Process link :'.$link->id);
                    $post_crawler = new PostCollector($site, $link);
                    $result = $post_crawler->process();
                    if(!$result['success']) {
                        $this->error($result['msg']);
                        continue;
                    }
                }
            }
        }elseif ($collect == 'content'){
            $posts_builer = Post::where('crawler', $site)
                ->where('is_handled', 0);

            while(true){
                $posts = $posts_builer->take($limit)->get();
                if(count($posts) == 0) break;

                foreach ($posts as $post){
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
