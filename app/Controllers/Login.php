<?php

namespace App\Controllers;

class Login extends BaseController
{
	public function __construct()
	{
	
	}
	
	public function index()
	{
		$data['inputs'] = $this->request->getPost();
		return view('login/login_view', $data);
	}
	
	public function logar()
	{
		$post = $this->request->getPost();
		
		$login = $post['login'] ?? '';
		$senha = $post['senha'] ?? '';
		
		// exemplo de utilização da regra criado para validar usuário validateUser
		//  $rules = [
		//     'email' => 'required|min_length[6]|max_length[50]|valid_email',
		//     'password' => 'required|min_length[8]|max_length[255]|validateUser[email,password]',
		// ];
		
		$validacao = $this->validate(['login' => 'required', 'senha' => 'required']);
		
		if (!$validacao) {
			return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
		}
		
		$checkUsuario = $this->dQuery("SELECT login, senha
	                FROM usuario
					WHERE login = '$login' and senha = '" . md5(trim($senha)) . "' and ativo=1");
		
		if (!$checkUsuario) {
			return redirect()->back()->withInput()->with('erro', 'Login ou senha inválidos');
		}
	}
}