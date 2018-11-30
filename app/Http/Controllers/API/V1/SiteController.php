<?php
/**
 * Created by PhpStorm.
 * User: TinyPoro
 * Date: 11/30/18
 * Time: 11:46 AM
 */

namespace App\Http\Controllers\API\V1;


use App\Category;
use App\Site;
use Illuminate\Http\Request;

class SiteController
{
    public function storeSite(Request $request){
        $url = $request->get('url');
        $type = $request->get('type');

        $category = $request->get('category_name');
        $category_obj = Category::where('name', $category)->first();

        if(!$category_obj){
            $meta = new Meta(200);
            $response = new Response();
            $response->setValue('data', $all_class);
            $resObj = new ResObj($meta, $response);

            return response()->json($resObj);
        }

        $seed_rule = $request->get('seed_rule');
        $paginate_rule = $request->get('paginate_rule');
        $title_rule = $request->get('title_rule');
        $content_rule = $request->get('content_rule');
        $post_paginate_rule = $request->get('post_paginate_rule');
        $strip_id = $request->get('strip_id');
        $strip_class = $request->get('strip_class');

        $rule = [
            'seed_rule' => $seed_rule,
            'paginate_rule' => $paginate_rule,
            'title_rule' => $title_rule,
            'content_rule' => $content_rule,
            'post_paginate_rule' => $post_paginate_rule,
            'strip_id' => $strip_id,
            'strip_class' => $strip_class,
        ];

        $stored = Site::create([
            'name' => 'Flight 10'
        ]);

        return response()->json($resObj);
    }
}