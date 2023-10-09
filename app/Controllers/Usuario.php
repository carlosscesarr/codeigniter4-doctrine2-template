<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Usuario extends BaseController
{
    public function index()
    {
		$post = $this->request->getPost();
		$this->filtroPost('seg_usuario',$post);
		$data['filtros'] = $post;
		
		$data['nomeClasse'] = 'usuario';
		$data['title'] = '';
		$data['acao'] = 'listar';
		$data['listagem'] = $this->getListagemIndex4('SegUsuario', 0, [],array("nome"=>"asc"),false);
        return view('usuario/usuario_view', $data);
    }
}
