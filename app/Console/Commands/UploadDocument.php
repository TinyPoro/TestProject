<?php

namespace App\Console\Commands;

use App\Post;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;

class UploadDocument extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:document
    {--site=loigiaihay : chọn site để crawl}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $site = $this->option('site');

        $posts_builder = Post::where('crawler', $site)
            ->where('is_handled', 1)
            ->where('is_uploaded', 0);

        $limit = 5;

        while(true){
            $posts = $posts_builder->take($limit)->get();
            if(count($posts) == 0) break;

            foreach($posts as $post){
                $this->info('Upload post :'.$post->id);
                $result = $this->uploadDocument($post);
                $code = $result->meta->code;

                if($code == 200){
                    $post->is_uploaded = 1;
                    $post->save();
                }else{
                    $post->is_uploaded = -1;
                    $this->info($result->meta->msg);
                    $post->save();
                    break;
                }
            }
        }
    }

    public function uploadDocument(Post $post){
        try {
            $main_server = config('crawler.main_server');
            $api_token = config('crawler.api_token');

            $response = $this->client->request('POST', $main_server.'/category/upload_post', [
                'headers' => [
                    'Authorization' => "Bearer $api_token"
                ],
                'form_params' => [
                    'title' => $post->title,
                    'content' => $post->content,
                    'subject' =>$post->subject,
                    'category_id' => $post->crawl_document->category_id
                ]
            ]);

            $res =  json_decode(trim($response->getBody()->getContents()));
            return $res;
        } catch (GuzzleException $e) {
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
