<?php

namespace App\Http\Controllers;

use App\Post;

use App\Http\Requests;

class PostController extends Controller
{
    //

    public function index($id){
		$post = Post::find($id);

		return view('frontend.posts.detail', ['post' => $post]);
	}

}
