<?php

namespace App\Controllers;


class Auth extends BaseController
{
	
	public function login()
	{
		view('auth/loginForm');
	}
	
	public function signin()
	{
		die('tete');
	}
	
	public function logout()
	{
		session()->destroy();
		redirect('auth/login');
	}
	
}