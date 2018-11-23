<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class AddFriend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:friend';

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

    private $total_requests = 400000000;
    private $requested = 0;
    private $request_per_user = 6000;

    private $global_access_token = null;
    private $global_username = 'tunglt4@topica.edu.vn';
    private $global_password = '123456a@';

    private $user_id_path = 'user_id.txt';
    private $user_ids = [];
    private $access_tokens = [];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->genUserIds();

        $this->global_access_token = $this->getAccessToken($this->global_username, $this->global_password);
        if(!$this->global_access_token){
            dump("Global info wrong!");
            return;
        }

        $this->getAllAccessToken();

        foreach($this->user_ids as $user_id ){

            $friend_ids = $this->getRandomFriendIds($this->request_per_user, $user_id);

            foreach ($friend_ids as $friend_id){
                $requested = $this->addFriend($user_id, $this->access_tokens[$user_id], $friend_id, $this->access_tokens[$friend_id]);

                if($requested){
                    dump("User ".$user_id." add $friend_id!");
                    $this->requested++;

                    if( $this->requested % 50000 === 0) {
                        $log_file = fopen("friend_request_log.txt", "w") or die("Unable to open file!");
                        fwrite($log_file, "Đã add ".$this->requested);
                        fclose($log_file);
                    }

                    if($this->total_requests === $this->requested) return;
                }
            }
        }
    }

    private function genUserIds(){
        $handle = fopen($this->user_id_path, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $this->user_ids[] = trim($line);
            }

            fclose($handle);
        } else {
            dump('error!');
        }
    }

    private function getAllAccessToken(){
        $remove_keys = [];

        foreach ($this->user_ids as $k => $user_id){
            $email = $this->getUserEmail($user_id);

            if(!$email) {
                $remove_keys[] = $k;
                continue;
            }

            if(!$this->isFakeUser($email)) {
                $remove_keys[] = $k;
                continue;
            }

            $access_token = $this->getAccessToken($email);

            if(!$access_token) {
                $remove_keys[] = $k;
                continue;
            }

            $this->access_tokens[$user_id] = $access_token;
        }

        foreach ($remove_keys as $remove_key){
            unset($this->user_ids[$remove_key]);
        }

        $this->user_ids = array_values($this->user_ids);
    }

    private function isFakeUser($email){
        if(!$email) return false;

        if(preg_match('/^p2tpc_/', $email)) return true;

        return false;
    }

    private function getUserEmail($user_id){
        $email = null;

        try{
            $response = $this->client->request(
                'GET',
                $this->host."/index.php/restful_api/user/$user_id?access_token=".$this->global_access_token
            );

            $res = json_decode($response->getBody()->getContents());

            try{
                $email = $res->data->email;
            }catch (\Exception $e){

            }

        }catch (GuzzleException $e){

        }catch (\Exception $e){

        }

        return $email;

    }

    private function getAccessToken( $username, $password = '123456789' ){

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
                        'username' => $username,
                        'password' => $password
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

    private function getRandomFriendIds($quantity, $except){
        $min = 0;
        $max = count($this->user_ids) - 1;

        $friend_ids=[];

        $random_keys = $this->getUniqueRandomNumbersWithinRange($min, $max, $quantity);

        foreach ($random_keys as $random_key){
            $friend_id = array_get($this->user_ids, $random_key, null);
            if(is_null($friend_id)) continue;

            if($friend_id === $except) continue;

            $friend_ids[] = $friend_id;
        }

        $friend_ids = array_unique($friend_ids);

        return $friend_ids;
    }

    public function getUniqueRandomNumbersWithinRange($min, $max, $quantity) {
        $numbers = range($min, $max);
        shuffle($numbers);
        return array_slice($numbers, 0, $quantity);
    }

    private function addFriend($from_id, $from_access_token, $to_id, $to_access_token){
        $send_request = $this->sendRequest($from_access_token, $to_id);

        if(!$send_request) return false;

        $accept_request = $this->acceptRequest($to_access_token, $from_id);

        return $accept_request;
    }

    private function sendRequest($from_access_token, $to_id){
        try{
            $response = $this->client->request(
                'POST',
                $this->host.'/index.php/restful_api/friend/request?access_token='.$from_access_token,
                [
                    'form_params' => [
                        'user_id' => $to_id,
                    ]
                ]
            );

            $data = json_decode($response->getBody()->getContents());

            return $data->status === "success";
        }catch (GuzzleException $e){

            return false;
        }
    }

    private function acceptRequest($to_access_token, $from_id){
        try{
            $response = $this->client->request(
                'PUT',
                $this->host.'/index.php/restful_api/friend/request?access_token='.$to_access_token,
                [
                    'form_params' => [
                        'user_id' => $from_id,
                        'action' => 'accept'
                    ]
                ]
            );

            $data = json_decode($response->getBody()->getContents());

            return $data->status === "success";
        }catch (GuzzleException $e){

            return false;
        }
    }
}
