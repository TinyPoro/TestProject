<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 7/12/16
 * Time: 14:25
 */

namespace App\Poro_Crawler\Contract;


use App\Models\CrawlDocument;

abstract class LinkCrawler extends WebPage{
	public $done = false;

	/**
	 * BaseLinkCollector constructor.
	 *
	 * @param $url
	 */
	public function __construct(array $config , $url = null, $driver = []) {
		parent::__construct($driver);
		$this->config = $config;
		if($url == null){
			$this->url = $this->config['start'];
		}else{
			$this->url = $url;
		}
	}

	/**
	 * Tao 1 crawl document tu thong tin hien tai
	 * @return CrawlDocument
	 */
	public function get_crawl_document($attributes = [], $save = false){
		$cd = new CrawlDocument();
		$cd->crawler = $this->crawler_name();
		$cd->url = $this->page->current_url();
		foreach($attributes as $k => $v){
			$cd->setAttribute($k, $v);
		}
		if($save){
			try{
				$cd->save();
			}catch (\Exception $ex){
				throw new \Exception("Không lưu được link " . $cd->url);
			}
		}
		return $cd;
	}

	abstract public function init_stack();

	abstract public function process();

	abstract public function crawler_name();

	abstract public function save_state();
	abstract public function resume();
	abstract public function clear_state_cached();
	abstract public function monitor();

}