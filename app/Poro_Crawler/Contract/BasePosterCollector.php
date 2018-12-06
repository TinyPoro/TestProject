<?php

namespace App\Poro_Crawler\Contract;


use App\Models\CrawlDocument;

abstract class BasePosterCollector extends WebPage {
    public $crawl_document = null;

    /**
     * @param array $config
     * @param CrawlDocument $crawlDocument
     * @param array $driver
     */
    public function __construct(array $config , CrawlDocument $crawlDocument, $driver = []) {
        parent::__construct($driver);
        $this->config = $config;
        $this->crawl_document = $crawlDocument;
        $this->url = $crawlDocument->url;

    }

}