<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Openbuildings\Spiderling\Driver_Phantomjs;
use Openbuildings\Spiderling\Driver_Phantomjs_Connection;
use Openbuildings\Spiderling\Node;
use Openbuildings\Spiderling\Page;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

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
        $phantomjs_driver_connection = new Driver_Phantomjs_Connection('http://localhost');
        $phantomjs_driver_connection->port(4445);
        $phantomjs_driver = new Driver_Phantomjs();
        $phantomjs_driver->connection($phantomjs_driver_connection);
        $page = new Page($phantomjs_driver);

        //login
        $this->visit($page, 'https://123doc.org/');

        if($this->find($page, '#GTM_Header_Click_Login')){
            $page->execute("showBoxLogin(document.getElementById('GTM_Header_Click_Login'));");

            $page->find('input[name=txtName]')->set('toppicktpc@gmail.com');
            $page->find('input[name=txtPass]')->set('toppick123');

            $page->execute("document.querySelector('.btn_login').click();");
            sleep(2);
        }

        //crawl
        $this->visit($page, 'https://123doc.org/doc-cat/415-toan-hoc.htm?t=xu-huong&e=tat-ca&p=mien-phi&l=tat-ca&v=grid&page=1');

        $els = $this->find($page, '.doc_list_cnt a:first-child');

        $els = array_map(function($el){
            /** @var $el \Openbuildings\Spiderling\Node */
            return [
                'text' => $el->text(),
                'url' => $this->convertLink('https://123doc.org/', $el->attribute('href'))
            ];
        }, $els->as_array());

        foreach ($els as $el){
            $url = $el['url'];

            $this->visit($page, $url);


        }

    }

    private function visit(Page $page, $url, $limit = 2){
        $old_url = $page->current_url();

        $page->visit($url);

        $wait = 0;

        while ($wait < $limit && $page->current_url() == $old_url){
            sleep(0.5);
            $wait += 0.5;
        }
    }

    public function all( Node $node, $selector, $type = 'css'){

        try{

            $els = $node->all( [$type, $selector] );

        } catch ( \Exception $e ){

            $els = [];

        }

        return $els;
    }

    public function find( Node $node, $selector, $type = 'css'){

        try{

            $el = $node->find( [$type, $selector] );

        } catch ( \Exception $e ){

            $el = null;

        }

        return $el;
    }

    public function convertLink($domain, $link){
        if(!$link) return null;

        $absolute_url = $this->rel2abs($link, $domain);
        return $absolute_url;
    }


    private function rel2abs($rel, $base)
    {
        /* return if already absolute URL */
        if (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;

        /* queries and anchors */
        if ($rel[0]=='#' || $rel[0]=='?') return $base.$rel;

        /* parse base URL and convert to local variables:
           $scheme, $host, $path */
        $parse_url = parse_url($base);
        $scheme = $parse_url['scheme'];
        $host = $parse_url['host'];
        $path = $parse_url['path'];

        /* remove non-directory element from path */
        $path = preg_replace('#/[^/]*$#', '', $path);

        /* destroy path if relative url points to root */
        if ($rel[0] == '/') $path = '';

        /* dirty absolute URL */
        $abs = "$host$path/$rel";

        /* replace '//' or '/./' or '/foo/../' with '/' */
        $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}

        /* absolute URL is ready! */
        return $scheme.'://'.$abs;
    }
}
