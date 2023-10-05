<?php


use App\Models\UsuarioModel;

class Usuariorules
{
	// public function custom_rule(): bool
	// {
	//     return true;
	// }
	
	/**
	 * @description Valida se o usuário existe através do email passado
	 * @param string $str
	 * @param string $fields
	 * @param array $data
	 * @return bool
	 */
	public function validateUser(string $str, string $fields, array $data)
	{
		$model = new UsuarioModel();
		$user = $model->where('email', $data['email'])
			->first();
		
		if (!$user)
			return false;
		
		return password_verify($data['password'], $user['password']);
	}
}
