<?php
/**
 * Created by PhpStorm.
 * User: TinyPoro
 * Date: 12/6/18
 * Time: 3:01 PM
 */

namespace App\Helper;


use Openbuildings\Spiderling\Node;

class CrawlHelper
{
    public static function rel2abs($rel, $base)
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

    public static function info($domain, $els){
        $els = array_map(function($el) use($domain){
            /** @var $el \Openbuildings\Spiderling\Node */
            return [
                'text' => $el->text(),
                'url' => self::convertLink($domain, $el->attribute('href'))
            ];
        }, $els->as_array());

        return $els;
    }

    private static function convertLink($domain, $link){
        $link = preg_replace('/^\.+/', '', $link);

        $absolute_url = self::rel2abs( $link, $domain);
        return $absolute_url;
    }

    public static function el(Node $node, $selector, $type = 'css'){
        sleep(1);

        try{
            $el = $node->find([$type, $selector]);
        }catch (\Exception $ex){
            $el = null;
        }
        return $el;
    }

    public static function els(Node $node, $selector, $type = 'css'){
        sleep(1);

        try{
            $el = $node->all([$type, $selector]);
        }catch (\Exception $ex){
            $el = [];
        }
        return $el;
    }
}