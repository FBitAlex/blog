<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use Illuminate\Http\Request;

class HomeController extends Controller {
	
	public function index() {
		$posts = Post::paginate(2);
		return view('pages.index', [
			'posts' 	=> $posts,
		]);
	}

	// public function getAuthor() {

	// 	return $this->author();
	// }
}
