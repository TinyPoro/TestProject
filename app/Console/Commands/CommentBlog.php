<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CommentBlog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comment:blog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $client;
    private $client_id = 'app-toppick';
//    private $client_id = '1';
    private $client_secret = '4409970dbafd2e0d53a4eb0ecb9bd3e84559a05c';
//    private $client_secret = '8a7c32a3b8de072fa276e1a7adf0260bdb6e90e8';

    private $host = 'https://phpfoxdev.toppick.vn';
//    private $host = 'http://phpfox.net';

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

    private $total_comments = 150000;
    private $commented = 0;
    private $comment_per_user = 6000;

    private $min_blog_id = 31;
    private $max_blog_id = 10851;

    private $comment_path = 'comments.txt';
    private $comments = [];

    private $friend_names = [];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->genComments();

        $users = User::all();

        while($users->count() > 0 ){

            $count = $users->count();

            $key = $this->getRandom(0, $count - 1);

            $user = $users[$key];

            $users->forget($key);

            $users = $users->values();

            $access_token = $this->getAccessToken($user);
            if(!$access_token) continue;

            $this->friend_names = $this->getListFriendName($access_token);

            $comment_blog_ids = $this->getUniqueRandomNumbersWithinRange($this->min_blog_id, $this->max_blog_id);

            foreach ($comment_blog_ids as $comment_blog_id){
                $commented = $this->commentBlog($access_token, $comment_blog_id);

                if($commented){
                    $this->commented++;

                    if( $this->commented % 10000 === 0) dump("Đã comment ".$this->commented);

                    if($this->total_comments === $this->commented) return;
                }
            }

            $this->friend_names = [];
        }
    }

    private function genComments(){
        $handle = fopen($this->comment_path, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $this->comments[] = trim($line);
            }

            fclose($handle);
        } else {
            dump('error!');
        }
    }

    private function getRandom($min = 0, $max ){
        return rand($min, $max);
    }

    public function getUniqueRandomNumbersWithinRange($min, $max) {
        $numbers = range($min, $max);
        shuffle($numbers);

        $quantity = $this->getRandom(200, $this->comment_per_user);

        return array_slice($numbers, 0, $quantity);
    }

    private function commentBlog( $access_token, $blog_id ){
        try{
            $text = $this->getRandomText();

            $response = $this->client->request(
                'POST',
                $this->host.'/index.php/restful_api/comment?access_token='.$access_token,
                [
                    'form_params' => [
                        'val' => [
                            'type' => 'blog',
                            'item_id' => $blog_id,
                            'text' => $text,
                        ]
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

    private function getRandomText(){
        shuffle( $this->comments);

        $text = array_get($this->comments, 0 , 'Bài viết hay quá!');
        if($text === "friend") {

            $text = $this->getTagFriend();

            if(!$text) $text = 'Bài viết hay quá!';
        }

        return $text;
    }

    private function getTagFriend(){
        $name = $this->getRandomFriendName();

        if(is_null($name)) return false;

        return "[user=12]".$name."[/user]";
    }

    private function getRandomFriendName(){
        shuffle($this->friend_names);

        return array_get($this->friend_names, 0 , null);
    }

    private function getListFriendName($access_token){
        $friend_names = [];

        try{

            $response = $this->client->request(
                'GET',
                $this->host.'/index.php/restful_api/friend?access_token='.$access_token
            );

            $result = json_decode($response->getBody()->getContents());

            $data = $result->data;

            foreach ($data as $item){

                $friend_names[] = $item->full_name;
            }
        }catch (GuzzleException $e){

        }catch (\Exception $e){

        }

        return $friend_names;

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

            return false;
        }
    }
}
