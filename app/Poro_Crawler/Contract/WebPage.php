<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 7/12/16
 * Time: 15:55
 */

namespace App\Poro_Crawler\Contract;


use League\Flysystem\Adapter\AbstractAdapter;
use Openbuildings\Spiderling\Driver_Phantomjs;
use Openbuildings\Spiderling\Driver_Phantomjs_Connection;
use Openbuildings\Spiderling\Driver_Selenium;
use Openbuildings\Spiderling\Driver_Selenium_Connection;
use Openbuildings\Spiderling\Driver_Simple;
use Openbuildings\Spiderling\Exception_Curl;
use Openbuildings\Spiderling\Exception_Notfound;
use Openbuildings\Spiderling\Page;

abstract class WebPage {

	public $config = [
		'domain' => '',
		'agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1',
		'start' => '',
		'link_collector' => '',
		'document_collector' => '',
		'callback' => '',
		'country' => '',
		'language' => '',
		'login' => [
			'url' => '',
			'accounts' => []
		],
		'logout' => '',
		'disk' => '',
		'dir' => '',
	];

	public $driver_name;

	/** @var Page */
	public $page;

    public $url;

	public function __construct($driver = []){
	    $this->driver_name = array_get($driver, 'name', 'simple');

		if(empty($driver) || $driver['name'] == 'simple'){

			$this->page = new Page();
		}elseif($driver['name'] == 'selenium'){

		    $selenium_driver = new Driver_Selenium();

			$this->page = new Page($selenium_driver);
		}elseif($driver['name'] == 'phantomjs'){
			if(isset($driver['server'])){
                $phantomjs_driver_connection = new Driver_Phantomjs_Connection('http://localhost/');
                $phantomjs_driver_connection->port(4445);
                $phantomjs_driver = new Driver_Phantomjs();
                $phantomjs_driver->connection($phantomjs_driver_connection);
			}else{
				$phantomjs_driver = new Driver_Phantomjs();
			}
			$this->page = new Page($phantomjs_driver);
		}
	}

	/**
	 * @param int $account_index
	 * @param null $back_link
	 *
	 * @return bool
	 * @throws CrawlExeption
	 * @throws \Openbuildings\Spiderling\Exception
	 */
	public function login($account_index = 0, $back_link = null){
		if(empty($this->config['login'])){
			return false;
		}
		if(empty($this->config['login']['url'])){
			return false;
		}
		if(empty($this->config['login']['accounts'])){
			return false;
		}
		if($back_link == null){
			$back_link = $this->page->current_url();
		}

		$this->page->visit($this->config['login']['url']);

		if(empty($this->config['login']['form'])){
			$form_selector = 'form';
		}else{
			$form_selector = $this->config['login']['form'];
		}
		if(empty($this->config['login']['submit'])){
			throw new CrawlExeption("Chưa xác định nút submit form đăng nhập");
		}else{
			$submit_selector = $this->config['login']['submit'];
		}

		if(empty($this->config['login']['accounts'][$account_index])){
			return false;
		}

		$account = $this->config['login']['accounts'][$account_index];

		try{
			$form = $this->page->find($form_selector);

			foreach($account as $k => $v){
				if(is_array($v)){
					if(empty($v['selector']) || empty($v['value'])){
						throw new CrawlExeption("Thiếu cài đặt selector/value cho " . $k);
					}
					$_selector =  $v['selector'];
					$_value = $v['value'];
				}else{
					$_selector = $k;
					$_value = $v;
				}
				$form->fill_in($_selector, $_value);
			}
			echo $form;

			try{
				$form->click_button($submit_selector);
			}catch (Exception_Curl $ex){
				\Log::error($ex->getMessage());
			}

			if($this->page->current_url() != $this->config['login']['url']){
				if($this->is_login()){
					$this->page->visit($back_link);
					return true;
				}
			}else{
				return false;
			}

		}catch (Exception_Notfound $ex){
			\Log::error($ex->getMessage());
			return false;
		}

		return false;
	}

	public function logout($back_link = ''){
		if(empty($back_link)){
			$back_link = $this->page->current_url();
		}
		if(empty($this->config['logout'])){
			throw new CrawlExeption("Chua dinh nghia lien ket dang xuat");
		}
		try{
			$this->page->visit($this->config['logout']);
		}catch (Exception_Curl $ex){

		}
		$this->page->visit($back_link);
		return !$this->is_login();
	}

	/**
	 * Download link hiện tại. Chú ý khi chọn chế độ force nên điền ext riêng.
	 * @param $file_name
	 * @param null $ext Chỉ khi nào không gán ext hoặc ext đúng bằng null thì mới tự xác đinh ext
	 * @param bool|false $force
	 *
	 * @return array|bool
	 */
	public function download_current($file_name, $ext = null, $force = false){
		if($this->page->driver() instanceof Driver_Simple ){

			if($force == false && $this->page->driver()->request_factory()->is_receiving_file()){
				return false;
			}

			// Chỉ khi nào không gán ext hoặc ext đúng bằng null thì mới tự xác đinh ext
			if($ext === null){

				/** @todo lấy từ file name ??? */

				// lấy từ header
				$ext = MimeTypeHelper::is_auto_download_header($this->page->driver()->request_factory()->header());

				// lấy từ file
				if(empty($ext)){
					$tmp_file = tempnam(sys_get_temp_dir(), 'file_crawled_tmp');
					file_put_contents($tmp_file, $this->page->content());
					$ext = MimeTypeHelper::guess_extension($tmp_file);
					@unlink($tmp_file);
				}
			}

			// Xác định ổ cứng lưu trữ
			/** @var AbstractAdapter $disk */
			$disk = \Flysystem::connection($this->config['disk']);

			// Chỉ thêm ext nếu xác định được
			if(!empty($ext)){
				$file_path = $file_name . "." . $ext;
			}else{
				$file_path = $file_name;
			}

			// ghi vào ổ cứng
			$disk->put($file_path, $this->page->content());

			return [
				'file_disk' => $this->config['disk'],
				'file_path' => $file_path
			];

		}else{
			\Log::alert('Chưa hỗ trợ download file khi dùng driver này');
			return false;
		}
	}

	public function is_login(){
		throw new CrawlExeption("Override it before use");
	}

    public function el($selector, $type = 'css'){
	    if($this->driver_name != 'simple') sleep(2);

        try{
            $el = $this->page->find([$type, $selector]);
        }catch (\Exception $ex){
            $el = null;
        }
        return $el;
    }

    public function els($selector, $type = 'css'){
        if($this->driver_name != 'simple') sleep(2);

        try{
            $el = $this->page->all([$type, $selector]);
        }catch (\Exception $ex){
            $el = [];
        }
        return $el;
    }

    public function convertLink($link){
        $url = $this->config['domain'];
        $link = preg_replace('/^\.+/', '', $link);

        $absolute_url = $this->rel2abs( $link, $url);
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