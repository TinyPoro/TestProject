<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class LikeBlog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'like:blog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $client;
//    private $client_id = 'app-toppick';
    private $client_id = '1';
//    private $client_secret = '4409970dbafd2e0d53a4eb0ecb9bd3e84559a05c';
    private $client_secret = '8a7c32a3b8de072fa276e1a7adf0260bdb6e90e8';
//
//    private $host = 'https://phpfoxdev.toppick.vn';
    private $host = 'http://phpfox.net';

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

    private $total_likes = 10000000;
    private $like_per_user = 2;

    private $min_blog_id = 1;
    private $max_blog_id = 4;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::all();

        while($users->count() > 0 ){

            $count = $users->count();

            $key = $this->getRandom(0, $count - 1);

            $user = $users[$key];

            $users->forget($key);

            $users = $users->values();

            $access_token = $this->getAccessToken($user);
            if(!$access_token) continue;

            $like_blog_ids = $this->getUniqueRandomNumbersWithinRange($this->min_blog_id, $this->max_blog_id, $this->like_per_user);

            foreach ($like_blog_ids as $like_blog_id){
                $liked = $this->likeBlog($access_token, $like_blog_id);

                if($liked){
                    $this->total_likes--;

                    if($this->total_likes === 0) return;
                }
            }
        }
    }

    private function getRandom($min = 0, $max ){
        return rand($min, $max);
    }

    public function getUniqueRandomNumbersWithinRange($min, $max, $quantity) {
        $numbers = range($min, $max);
        shuffle($numbers);
        return array_slice($numbers, 0, $quantity);
    }

    private function likeBlog( $access_token, $blog_id ){
        try{
            $response = $this->client->request(
                'POST',
                $this->host.'/index.php/restful_api/like?access_token='.$access_token,
                [
                    'form_params' => [
                        'type_id' => 'blog',
                        'item_id' => $blog_id
                    ]
                ]
            );

            $data = json_decode($response->getBody()->getContents());

            return $data->status === "success";
        }catch (GuzzleException $e){

            return false;
        }catch (\Exception $e){

            return false;
        }
    }

    private function getAccessToken( User $user){

        try{
            $response = $this->client->request(
                'POST',
                $this->host.'/index.php/restful_api/token',
                [
                    'auth' => [
                        $this->client_id,
                        $this->client_secret
                    ],
                    'form_params' => [
                        'grant_type' => 'password',
                        'username' => $user->getEmail(),
                        'password' => '123456789'
                    ]
                ]
            );

            $data = json_decode($response->getBody()->getContents());

            $access_token = $data->access_token;

            return $access_token;
        }catch (GuzzleException $e){
            dump($e->getMessage());

            return false;
        }
    }
}
