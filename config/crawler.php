<?php

return [
	'sites' => [
	    'link' => [
            'vietjack' => [
                'domain' => 'https://vietjack.com/',
                'agent' => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
                'start' => 'https://vietjack.com/',
                'callback' => '',
                'disk' => 'crawled_file',

                'rule' => [
                    [//lớp học
                        'type' => 'link',
                        'selector' => '.navbar-header ul.navbar-nav > li:nth-child(n+2) > a'
                    ],
                    [//môn học
                        'type' => 'link',
                        'selector' => '.middle-col > ul.list > li > a'
                    ],
                    [
                        'type' => 'get_link',
                        'item_selector' => '.content .middle-col ul[class=\'list\'] > li',
                    ],
                ],
            ],

            'loigiaihay' => [
                'domain' => 'https://loigiaihay.com/',
                'agent' => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
                'start' => 'https://loigiaihay.com/',
                'callback' => '',
                'disk' => 'crawled_file',
                'rule' => [
                    [//môn học
                        'type' => 'link',
                        'selector' => "div.Tabs.taborange.clearfix > h2 > a",
                        'exclude_texts' => [
                            'TRUYỆN CỔ TÍCH',
                            'MÔN ĐẠI CƯƠNG'
                        ]
                    ],
                    [//lớp học
                        'type' => 'link',
                        'selector' => 'div.content_box > div.box_class.bottom10.clearfix > div.col3 > a'
                    ],
                    [
                        'type' => 'loop',
                        'start_selector' => "//div[contains(@class,'content_box')]/div[contains(@class,'box clearfix')]/div[contains(@class,'subject')]",
                        'loop_selector' => "/ul/li",
                        'start_text_selector' => [
                            "/h2/a",
                            "/h3/a"
                        ],
                        'text_selector' => ["/a"]
                    ],
                ],
            ],
            'hoc247' => [
                'domain' => 'https://hoc247.net/',
                'agent' => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
                'start' => 'https://hoc247.net/',
                'callback' => '',
                'disk' => 'crawled_file',
                'rule' => [
                    [//chương trình
                        'type' => 'link',
                        'selector' => ".box-inline-block .col-sm-4:first-child ul li a",
                    ],
                    [//môn học
                        'type' => 'get_link',
                        'item_selector' => '.list-cate li a'
                    ],
                ],
            ],
        ],
        'post' => [
            'vietjack' => [
                'domain' => 'https://vietjack.com/',
                'agent' => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
                'start' => 'https://vietjack.com/',
                'callback' => '',
                'disk' => 'crawled_file',

                'rule' => [
                    [//lớp học
                        'type' => 'other',
                        'parent_selector' => '.content .middle-col'
                    ],
                ],
            ],

            'loigiaihay' => [
                'domain' => 'https://loigiaihay.com/',
                'agent' => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
                'start' => 'https://loigiaihay.com/',
                'callback' => '',
                'disk' => 'crawled_file',
                'rule' => [
                    [//môn học
                        'type' => 'links',
                        'selector' => '.content_box ul.list > li > h3 > a',
                    ],
                ],
            ],
            'hoc247' => [
                'domain' => 'https://hoc247.net/',
                'agent' => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
                'start' => 'https://hoc247.net/',
                'callback' => '',
                'disk' => 'crawled_file',
                'driver' => [
                    'name' => 'phantomjs',
                    'server' => 'phantomjs'
                ],
                'rule' => [
                    [//môn học
                        'type' => 'click',
                        'selector' => '.cate-col-right li p a',
                    ],
                ],
            ],
        ],
        'content' => [
            'vietjack' => [
                'domain' => 'https://vietjack.com/',
                'agent' => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
                'start' => 'https://vietjack.com/',
                'callback' => '',
                'disk' => 'crawled_file',

                'rule' => [
                    [//lớp học
                        'type' => 'other',
                        'parent_selector' => '.content .middle-col'
                    ],
                ],
            ],

            'loigiaihay' => [
                'domain' => 'https://loigiaihay.com/',
                'agent' => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
                'start' => 'https://loigiaihay.com/',
                'callback' => '',
                'disk' => 'crawled_file',
                'rule' => [
                    [//môn học
                        'type' => 'html',
                        'selector' => '.detail_new *',
                        'valid_selector' => [
                            'p', 'table', 'img'
                        ]
                    ],
                ],
            ],
            'hoc247' => [
                'domain' => 'https://hoc247.net/',
                'agent' => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
                'start' => 'https://hoc247.net/',
                'callback' => '',
                'disk' => 'crawled_file',
                'rule' => [
                    [
                        'type' => 'html',
                        'selector' => '#itvc20player > *',
                    ],

                ],
            ],
        ]
	],
    'sitemap' => [
        'post' => [
            'hoc247' => [
                'domain' => 'https://hoc247.net/',
                'agent' => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
                'start' => 'https://hoc247.net/sitemaps/lession.xml',
                'callback' => '',
                'selector' => 'loc',
            ],
        ],
    ],
    'news' => [
        'post' => [
            'easyuni' => [
                'domain' => 'https://www.easyuni.vn/',
                'agent' => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
                'end_point' => 'https://www.easyuni.vn/advice/?page=',
                'callback' => '',
                'news_selector' => '.card-columns a',
                'title_selector' => '.card-title',
            ],
        ],
        'content' => [
            'easyuni' => [
                'domain' => 'https://www.easyuni.vn/',
                'agent' => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
                'end_point' => 'https://www.easyuni.vn/advice/?page=',
                'callback' => '',
                'selector' => '.s-content *',
            ],
        ],
    ],
	'cache_dir' => storage_path('crawler'),
	'main_server' => env('MAIN_SERVER', 'http://miny.net/api/v1'),
	'api_token' => env('API_TOKEN', 'ok3xzAohHlOWW0J6RjSNozoOPrAsn5qHwP34eC4ydEknPdlGQO'),
];