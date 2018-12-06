<?php

namespace App\Console\Commands;

use App\Helper\CrawlHelper;
use Illuminate\Console\Command;
use Openbuildings\Spiderling\Driver_Phantomjs;
use Openbuildings\Spiderling\Driver_Phantomjs_Connection;
use Openbuildings\Spiderling\Page;

class CrawlFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:crawler_files
    {--site=123doc : chọn site để crawl}
    {--collect=file : chọn loại crawl post/content}';

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
        $phantomjs_driver_connection = new Driver_Phantomjs_Connection('http://localhost/');
        $phantomjs_driver_connection->port(4445);
        $phantomjs_driver = new Driver_Phantomjs();
        $phantomjs_driver->connection($phantomjs_driver_connection);
        $page = new Page($phantomjs_driver);

        $cur_page_number = 1;
        $domain = 'https://123doc.org/';
        $end_point = 'https://123doc.org/doc-cat/371-lop-9.htm?t=xu-huong&page=';

        $page->visit($domain);
        $page->execute('showBoxLogin(document.getElementById(\'GTM_Header_Click_Login\'));');
        sleep(3);

        try{
            $page->find('input[name="txtName"]')->set('toppicktpc@gmail.com');
            $page->find('input[name="txtPass"]')->set('toppick123');
            $page->click_button('.btn_login');
        }catch (\Exception $e){
dd($e->getMessage());
        }
        sleep(1);

        while($cur_page_number <= 100){
            try{
                $page->visit($end_point.$cur_page_number);

                $doc_eles = CrawlHelper::els($page, '.doc_list_cnt a');

                $doc_infos = CrawlHelper::info($domain, $doc_eles);

                foreach ($doc_infos as $doc_info){
                    $text = array_get($doc_info, 'text', '');
                    $url = array_get($doc_info, 'url', '');

                    $page->visit($url);

                    $download_free_button = CrawlHelper::el($page, '.detailDownload a:nth-child(2)');
                    if($download_free_button){
                        dd($download_free_button->attribute('onclick'));
                        sleep(3);
                        dd($page->current_url());
                    }
                }

            }catch (\Exception $e){
                dd($e->getMessage());
                break;
            }
            $cur_page_number++;
        }

    }

    private function download($url = ''){
        $path = "/absolute_path_to_your_files/"; // change the path to fit your websites document structure

        $dl_file = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).]|[\.]{2,})", '', $_GET['download_file']); // simple file name validation
        $dl_file = filter_var($dl_file, FILTER_SANITIZE_URL); // Remove (more) invalid characters
        $fullPath = $path.$dl_file;

        if ($fd = fopen ($fullPath, "r")) {
            $fsize = filesize($fullPath);
            $path_parts = pathinfo($fullPath);
            $ext = strtolower($path_parts["extension"]);
            switch ($ext) {
                case "pdf":
                    header("Content-type: application/pdf");
                    header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
                    break;
                // add more headers for other content types here
                default;
                    header("Content-type: application/octet-stream");
                    header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
                    break;
            }
            header("Content-length: $fsize");
            header("Cache-control: private"); //use this to open files directly
            while(!feof($fd)) {
                $buffer = fread($fd, 2048);
                echo $buffer;
            }
        }
        fclose ($fd);
    }
}
