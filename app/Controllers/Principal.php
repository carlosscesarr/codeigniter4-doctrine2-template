<?php

namespace App\Controllers;

class Principal extends BaseController
{
	public function index()
	{
		$data = [];
		return view('principal_view', $data);
	}
	
}
