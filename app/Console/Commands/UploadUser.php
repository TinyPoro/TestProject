<?php

namespace App\Console\Commands;

use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;

class UploadUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:user';

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
//
    private $username = 'tunglt4@topica.edu.vn';
//    private $username = 'ngophuongtuan@gmail.com';
    private $password = '123456a@';
//    private $password = 'tinyporo1817';

    private $access_token;

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
        $this->access_token = $this->getAccessToken();

        $users = User::where('uploaded', 0)->get();

        while($users->count() > 0 ){
            $count = $users->count();

            $key = $this->getRandom(0, $count - 1);

            $user = $users[$key];

            $uploaded = $this->uploadUser($user);

            if($uploaded) $user->uploaded = 1;
            else {

                $this->access_token = $this->getAccessToken();

                $uploaded = $this->uploadUser($user);

                if($uploaded) $user->uploaded = 1;
                else $user->uploaded = -1;

            }

            $user->save();

            $users->forget($key);

            $users = $users->values();
        }

    }

    private function getRandom($min = 0, $max ){
        return rand($min, $max);
    }

    private function uploadUser(User $user){
        try{
            $response = $this->client->request(
                'POST',
                $this->host.'/index.php/restful_api/user?access_token='.$this->access_token,
                [
                    'form_params' => [
                        'val' => [
                            'email' => $user->getEmail(),
                            'full_name' => $user->name,
                            'password' => '123456789',
                            'user_name' => $user->getUserName()
                        ]
                    ]
                ]
            );

            $data = json_decode($response->getBody()->getContents());

            return $data->status === "success";
        }catch (GuzzleException $e){
            dump($e->getMessage());

            return false;
        }catch (\Exception $e){
            dump($e->getMessage());

            return false;
        }
    }

    private function getAccessToken(){

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
                        'username' => $this->username,
                        'password' => $this->password
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
