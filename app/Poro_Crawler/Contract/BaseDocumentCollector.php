<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 7/12/16
 * Time: 14:30
 */

namespace App\Poro_Crawler\Contract;


use App\Post;

abstract class BaseDocumentCollector extends WebPage{

	public $post = null;

	/**
	 * @param array $config
	 * @param Post $post
	 * @param array $driver
	 */
	public function __construct(array $config , Post $post, $driver = []) {
		parent::__construct($driver);
		$this->config = $config;
		$this->post = $post;
		$this->url = $post->url;

	}

	public function build_path($file = null){
		$path = date('Y/m_d');
		if(!empty($this->config['dir'])){
			$path = $this->config['dir'] . "/" . $path;
		}
		if($file != null){
			$path .= "/" . $file;
		}
		return $path;
	}

	public function build_file_name(){
		return md5($this->page->current_url());
	}

	abstract public function process($fail_status = -1);

}