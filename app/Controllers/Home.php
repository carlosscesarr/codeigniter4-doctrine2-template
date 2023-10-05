<?php

namespace App\Controllers;

use Config\Database;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

use App\Libraries\Doctrine;


class Home extends BaseController
{
	public function index()
	{
		return view('home_view');
	}
	
}
