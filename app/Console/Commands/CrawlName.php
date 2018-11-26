<?php

namespace App\Console\Commands;

use App\User;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class CrawlName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:name';

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

    private $ho = [
        'Nguyễn',
        'Trần',
        'Lê',
        'Phạm',
        'Hoàng',
        'Huỳnh',
        'Phan',
        'Vũ',
        'Võ',
        'Đặng',
        'Bùi',
        'Đỗ',
        'Hồ',
        'Ngô',
        'Dương',
        'Lý',
    ];



    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

//        $this->getLastName1();
//        $this->getLastName2();
//        $this->getLastName3();
//        $this->getLastName4();
        $this->getLastName5();

    }

    private function getLastName1(){
        $client = new Client();

        $response = $client->get("http://me.phununet.com/WikiPhununet/ChiTietWiki.aspx?m=0&StoreID=3878&fbclid=IwAR3MEOAFQx5aS3NMWRm_xolE5LJ4B7YwLUpmR3KNCxholV4MI_ziVlFSBWk");

        $html = $response->getBody()->getContents();

        $crawler = new Crawler();
        $crawler->addHtmlContent($html);

        $crawler->filter('table li')->each( function( Crawler $node){
            $last_name = $node->text();

            $this->genName( $last_name );
        });
    }

    private function getLastName2(){
        $handle = fopen("name.txt", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $this->genName($line);
            }

            fclose($handle);
        } else {
            dump('error!');
        }
    }

    private function getLastName3(){
        $client = new Client();

        $response = $client->get("http://www.ten-be.com/c/t%C3%AAn%20ph%E1%BB%95%20bi%E1%BA%BFn%20t%E1%BA%A1i%20Vi%E1%BB%87t%20Nam?fbclid=IwAR01123ntxCZqRP1C0HF8vjn8RQ_VzfYLzxstmRmCa9m1VDSvyuvG1oBhKA");

        $html = $response->getBody()->getContents();

        $crawler = new Crawler();
        $crawler->addHtmlContent($html);

        $invalids = [
            '    Tên',
            'Monday 19 November 2018 @ 14:17',
            'Ko co hong anh ha ban'
        ];

        $crawler->filter('table tr td:nth-child(2)')->each( function( Crawler $node) use( $invalids){
            $last_name = trim($node->text());

            if(in_array($last_name, $invalids)){

                return;
            }

            $this->genName( $last_name );
        });
    }

    private function getLastName4(){
        $client = new Client();

        $response = $client->get("http://www.ten-be.com/c/T%C3%AAn%20ti%E1%BA%BFng%20Anh%20ph%E1%BB%95%20bi%E1%BA%BFn");

        $html = $response->getBody()->getContents();

        $crawler = new Crawler();
        $crawler->addHtmlContent($html);

        $invalids = [
            '    Tên',
        ];

        $crawler->filter('table tr td:nth-child(2)')->each( function( Crawler $node) use( $invalids){
            $last_name = trim($node->text());

            if(in_array($last_name, $invalids)){

                return;
            }

            $this->genName( $last_name, true );
        });
    }

    private function getLastName5(){
        $client = new Client();

        $response = $client->get("https://giasutoeic.com/ten-tieng-anh/");

        $html = $response->getBody()->getContents();

        $crawler = new Crawler();
        $crawler->addHtmlContent($html);

        $crawler->filter('table tr td:nth-child(1)')->each( function( Crawler $node){
            $last_name = trim($node->text());

            $this->genName( $last_name, true );
        });
    }

    private function genName( $last_name, $reverse = false ){

        $last_name = trim($last_name);

        foreach ( $this->ho as $ho ){

            if($reverse) $name = "$last_name $ho";
            else $name = "$ho $last_name";
            $email = $this->genEmail($name);

            try{
                User::create([
                    'name' => $name,
                    'email' => $email,
                ]);
            } catch ( \Exception $e){
                continue;
            }
        }
    }

    private function genEmail( $name ){
        $name = $this->vn_to_str( $name );

        $words = explode(' ', $name);

        $words = array_map('mb_strtolower', $words);

        $prefix_email = implode('_', $words);

        $i = 0;

        do{

            if( $i === 0) $email = $prefix_email."@gmail.com";
            else $email = $prefix_email."$i@gmail.com";

            $i++;

            $existed_email_count = User::where('email', $email)->count();

        } while ( $existed_email_count > 0 );


        return $email;
    }

    private function vn_to_str ($str){

        $unicode = array(

            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',

            'd'=>'đ',

            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',

            'i'=>'í|ì|ỉ|ĩ|ị',

            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',

            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',

            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',

            'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',

            'D'=>'Đ|Ð',

            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',

            'I'=>'Í|Ì|Ỉ|Ĩ|Ị',

            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',

            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',

            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',

        );

        foreach($unicode as $nonUnicode=>$uni){

            $str = preg_replace("/($uni)/i", $nonUnicode, $str);

        }
        $str = str_replace(' ','_',$str);

        return $str;

    }
}
