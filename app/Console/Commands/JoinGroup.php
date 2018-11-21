<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class JoinGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'join:group';

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

    private $total_joins = 725000;
    private $join_per_user = 70;

    private $group_path = 'group_id.txt';
    private $group_ids = [];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->genGroupIds();

        $users = User::all();

        while($users->count() > 0 ){

            $count = $users->count();

            $key = $this->getRandom(0, $count - 1);

            $user = $users[$key];

            $users->forget($key);

            $users = $users->values();

            $access_token = $this->getAccessToken($user);
            if(!$access_token) continue;

            $join_group_ids = $this->getRandomGroupIds($this->join_per_user);

            foreach ($join_group_ids as $join_group_id){
                $joined = $this->joinGroup($access_token, $join_group_id);
                dump("Join: ".$user->getUserName()." voi group id " . $join_group_id);
                dump("Ket qua: ".$joined);
                if($joined){
                    $this->total_joins--;

                    if($this->total_joins === 0) return;
                }
            }
        }
    }

    private function genGroupIds(){
        $handle = fopen($this->group_path, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $this->group_ids[] = trim($line);
            }

            fclose($handle);
        } else {
            dump('error!');
        }
    }

    private function getRandom($min = 0, $max ){
        return rand($min, $max);
    }

    private function getRandomGroupIds($quantity){
        $min = 0;
        $max = count($this->group_ids) - 1;

        $join_group_ids=[];

        $random_keys = $this->getUniqueRandomNumbersWithinRange($min, $max, $quantity);

        foreach ($random_keys as $random_key){
            $join_group_id = array_get($this->group_ids, $random_key, null);
            if(is_null($join_group_id)) continue;

            $join_group_ids[] = $join_group_id;
        }

        return $join_group_ids;
    }

    public function getUniqueRandomNumbersWithinRange($min, $max, $quantity) {
        $numbers = range($min, $max);
        shuffle($numbers);
        return array_slice($numbers, 0, $quantity);
    }

    private function joinGroup( $access_token, $group_id ){
        try{
            $response = $this->client->request(
                'POST',
                $this->host.'/index.php/restful_api/like?access_token='.$access_token,
                [
                    'form_params' => [
                        'type_id' => 'groups',
                        'item_id' => $group_id
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

            return false;
        }
    }
}
