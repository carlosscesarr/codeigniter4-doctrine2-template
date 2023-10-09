<?php

namespace App\Controllers;

use App\Libraries\Doctrine;
use App\Libraries\Pagination;
use App\Models\AuditoriaModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
	/**
	 * Instance of the main Request object.
	 *
	 * @var CLIRequest|IncomingRequest
	 */
	protected $request;
	
	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	protected $helpers = [];
	
	/**
	 * Be sure to declare properties for any property fetch you initialized.
	 * The creation of dynamic property is deprecated in PHP 8.2.
	 */
	// protected $session;
	
	/**
	 * @var Doctrine
	 */
	protected $doctrine;
	
	/**
	 * @var array
	 */
	protected $dadosUsuarioLogado;
	
	/**
	 * @return void
	 */
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);
		$this->doctrine = new Doctrine();
		
		$this->dadosUsuarioLogado = session()->get('dados_usuario_logado');
		
		// Preload any models, libraries, etc, here.
		
		// $this->session = \Config\Services::session();
	}
	
	public function dQuery($sql, $parametros = [], $showSQL = false)
	{
		
		$conn = $this->doctrine->em->getConnection();
		if ($showSQL) {
			var_dump($sql);
			var_dump($parametros);
		}
		
		$param_tipos = [];
		if (count($parametros) > 0) {
			foreach ($parametros as $p) {
				switch (gettype($p)) {
					case 'integer':
						$param_tipos[] = \PDO::PARAM_INT;
						break;
					case 'array':
						$param_tipos[] = \Doctrine\DBAL\Connection::PARAM_STR_ARRAY;
						break;
					default:
						$param_tipos[] = \Doctrine\DBAL\Types\Type::STRING;
						break;
				}
			}
		}
		try {
			
			$stmt = $conn->executeQuery($sql, $parametros, $param_tipos);
			return $stmt->fetchAllAssociative();
			
		} catch (\Exception $e) {
			var_dump($e->getMessage());
		}
	}
	
	public function auditoria($id = 0, $tabela = "", $chave = "id")
	{
		/**@var AuditoriaModel $auditoria * */
		$auditoria = model("AuditoriaModel");
		$data['auditorias'] = $auditoria->getAuditoriaId($id, $tabela, $chave);
		$data['chave'] = $chave;
		view('auditoria/auditoria_view', $data);
	}
	
	public function getListagemIndex4($Entity, $pg = -1, $where = [], $order = ['id' => 'desc'], $show_sql = false, $metodoPaginacao = '', $intervaloPaginacao = 10)
	{
		
		if ($pg > 0) {
			session()->set('dados_pg', [$pg]);
		} else {
			session()->remove('dados_pg');
		}
		
		if ($show_sql) {
			$this->showSQL();
		}
		
		eval("\$classe = new \App\Entity\\" . $Entity . ";");
		$meta = $this->doctrine->em->getClassMetadata(get_class($classe));
		$chavePrimaria = $meta->identifier[0];
		
		$por_pagaina = $intervaloPaginacao;
		
		$rs_cont = $this->doctrine->em->getRepository("\App\Entity\\" . $Entity)->createQueryBuilder('en');
		$rs_cont->select('COUNT(en.' . $chavePrimaria . ')');
		
		$rs = $this->doctrine->em->getRepository("\App\Entity\\" . $Entity)->createQueryBuilder('en');
		
		$lista = [];
		$cont_campo = 1;
		
		//definindo alias
		$alias = [];
		$whereGrupoOr = [];
		$whereMaior = [];
		$notIn = [];
		$in = [];
		$like = [];
		$between = [];
		
		$whereAuxiliar = $where;
		$where = [];
		
		foreach ($whereAuxiliar as $key => $value) {
			if (strpos($key, '::in')) {
				$nomeCampo = explode('::in', $key)[0];
				$in[$nomeCampo] = $value;
				$key = str_replace('::in', '', $key);
			} elseif (strpos($key, '::like')) {
				$nomeCampo = explode('::like', $key)[0];
				$like[$nomeCampo] = $value;
				$key = str_replace('::like', '', $key);
			} elseif (strpos($key, '::not_in')) {
				$nomeCampo = explode('::not_in', $key)[0];
				$notIn[$nomeCampo] = $value;
				$key = str_replace('::not_in', '', $key);
			} elseif (strpos($key, '::group_or')) {
				$nomeCampo = explode('::group_or', $key)[0];
				$whereGrupoOr[$nomeCampo] = $value;
				$key = str_replace('::group_or', '', $key);
			} elseif (strpos($key, '::>')) {
				$nomeCampo = explode('::>', $key)[0];
				$whereMaior[$nomeCampo] = $value;
				$key = str_replace('::>', '', $key);
			} elseif (strpos($key, '::between')) {
				$nomeCampo = explode('::between', $key)[0];
				$between[$nomeCampo] = $value;
				$key = str_replace('::between', '', $key);
			}
			$where[$key] = $value;
		}
		
		$complemento1 = '';
		foreach ($where as $key => $value) {
			$campo = explode('.', $key);
			$countExplodeCampos = count($campo);
			$cont_campo = 1;
			
			if ($countExplodeCampos > 1) {
				if (array_key_exists($campo[0], $alias)) {
					$cont_campo = $alias[$campo[0]] + 1; //soma mais um pq logo abaixo subtrai
				}
				
				for ($i = 0; $i < $countExplodeCampos; $i++) {
					$indiceDoUltimoCampo = $countExplodeCampos - 1;
					$checouNoUltimoCampo = $i == $indiceDoUltimoCampo;
					
					if ($checouNoUltimoCampo) {
						$indiceDoPenultimoCampo = $i - 1;
						
						$ali = 'p' . ($alias[$campo[$indiceDoPenultimoCampo]]);
						
						$nomeColunaFiltro = $campo[$indiceDoUltimoCampo];
						
						$param = uniqid();
						$nomeParametroColuna = $nomeColunaFiltro . $param;
						$parametroColuna = ":" . $nomeParametroColuna;
						
						$operador = '=';
						$metodo = 'andWhere';
						$parametroComplemento = null;
						
						if ($value == 'IS NULL' || $value == 'IS NOT NULL') {
							// Executando logo pois essas duas condições não dá pra usar no setParameter
							$rs_cont->{$metodo}("$ali.$nomeColunaFiltro $value");
							$rs->{$metodo}("$ali.$nomeColunaFiltro $value");
							continue;
						}
						
						if (array_key_exists($key, $between)) {
							$intervalo1 = $between[$key][0];
							$intervalo2 = $between[$key][1];
							$operador = 'BETWEEN';
							
							$rs_cont->{$metodo}("$ali.$nomeColunaFiltro $operador $intervalo1 AND $intervalo2");
							$rs->{$metodo}("$ali.$nomeColunaFiltro $operador $intervalo1 AND $intervalo2");
							continue;
							
						}
						
						if (array_key_exists($key, $whereGrupoOr)) {
							$value = $whereGrupoOr[$key];
							$metodo = strlen($complemento1) > 0 ? ' OR ' : '';
							if (stristr($value, 'LIKE')) {
								$value = trim(str_ireplace('LIKE', '', $value));
								$operador = 'LIKE';
							}
							
							$complemento1 .= "$operador $ali.$nomeColunaFiltro $operador $value";
							continue;
						}
						
						if (array_key_exists($key, $in)) {
							$operador = 'IN';
							$parametroComplemento = \Doctrine\DBAL\Connection::PARAM_STR_ARRAY;
						} elseif (array_key_exists($key, $like)) {
							$operador = 'LIKE';
							$value = trim(str_ireplace("'", '', str_ireplace('LIKE', '', $value)));
						} elseif (array_key_exists($key, $notIn)) {
							$operador = 'NOT IN';
							$parametroComplemento = \Doctrine\DBAL\Connection::PARAM_STR_ARRAY;
						} elseif (array_key_exists($key, $whereMaior)) {
							$operador = '>';
						}
						
						if ($operador == 'IN' || $operador == 'NOT IN') {
							$parametroColuna = "($parametroColuna)";
						}
						
						$rs_cont->{$metodo}("$ali.$nomeColunaFiltro $operador $parametroColuna")->setParameter($nomeParametroColuna, $value, $parametroComplemento);
						$rs->{$metodo}("$ali.$nomeColunaFiltro $operador $parametroColuna")->setParameter($nomeParametroColuna, $value, $parametroComplemento);
						continue;
					}
					
					if (!array_key_exists($campo[$i], $alias)) {
						$ehPrimeiroCampoRelacionamento = $cont_campo == 1;
						$campoRelacionamento = $campo[$i];
						$contCampoAnteior = $cont_campo - 1;
						
						$alias[$campoRelacionamento] = count($alias) + 1;
						$rs_cont->join(($ehPrimeiroCampoRelacionamento ? 'en' : 'p' . $contCampoAnteior) . '.' . $campoRelacionamento, 'p' . ($alias[$campoRelacionamento]));
						$rs->join(($ehPrimeiroCampoRelacionamento ? 'en' : 'p' . $contCampoAnteior) . '.' . $campoRelacionamento, 'p' . ($alias[$campoRelacionamento]));
						// $teste = ($ehPrimeiroCampoRelacionamento ? 'en' : 'p'.$contCampoAnteior);
						// echo "join: " . $teste .'.'.$campoRelacionamento. ' | p'.($alias[$campoRelacionamento]) ."<br><br>";
						$cont_campo = count($alias) + 1;
					}
				}
				
				continue;
			}
			
			$operador = '=';
			$metodo = 'andWhere';
			$parametroComplemento = null;
			$parametroColuna = ':' . $key;
			
			if ($value == 'IS NULL' || $value == 'IS NOT NULL') {
				// Executando logo pois essas duas condições não dá pra usar no setParameter
				$rs->{$metodo}("en.$key $value");
				$rs_cont->{$metodo}("en.$key $value");
				continue;
			}
			
			if (array_key_exists($key, $between)) {
				$intervalo1 = $between[$key][0];
				$intervalo2 = $between[$key][1];
				$operador = 'BETWEEN';
				
				$rs_cont->{$metodo}("en.$key $operador $intervalo1 AND $intervalo2");
				$rs->{$metodo}("en.$key $operador $intervalo1 AND $intervalo2");
				continue;
			}
			
			if (array_key_exists($key, $whereGrupoOr)) {
				$value = $whereGrupoOr[$key];
				$metodo = strlen($complemento1) > 0 ? ' or ' : '';
				if (stristr($value, 'LIKE')) {
					$value = trim(str_ireplace('LIKE', '', $value));
					$operador = 'LIKE';
				}
				
				$complemento1 .= "$metodo en.$key $operador $value";
				
				continue;
			}
			
			if (array_key_exists($key, $in)) {
				$operador = 'IN';
				$parametroComplemento = \Doctrine\DBAL\Connection::PARAM_STR_ARRAY;
			} elseif (array_key_exists($key, $like)) {
				$operador = 'LIKE';
				$value = trim(str_ireplace("'", '', str_ireplace('LIKE', '', $value)));
			} elseif (array_key_exists($key, $notIn)) {
				$operador = 'NOT IN';
				$parametroComplemento = \Doctrine\DBAL\Connection::PARAM_STR_ARRAY;
			} elseif (array_key_exists($key, $whereMaior)) {
				$operador = '>';
			}
			
			if ($operador == 'IN' || $operador == 'NOT IN') {
				$parametroColuna = "($parametroColuna)";
			}
			
			$rs_cont->{$metodo}("en.$key $operador $parametroColuna")->setParameter($key, $value, $parametroComplemento);
			$rs->{$metodo}("en.$key $operador $parametroColuna")->setParameter($key, $value, $parametroComplemento);
		}
		
		if (count($whereGrupoOr) > 0) {
			$metodo = 'andWhere';
			$rs_cont->{$metodo}("($complemento1)");
			$rs->{$metodo}($complemento1);
		}
		
		$total_registros = $rs_cont->getQuery()->getSingleScalarResult();
		
		if ($total_registros > 0) {
			
			foreach ($order as $key => $val) {
				$rs->addOrderBy("en.$key", $val);
			}
			
			if ($pg >= 0) {
				$rs->setMaxResults($por_pagaina);
				$rs->setFirstResult($pg);
			}
			
			$query = $rs->getQuery();
			
			if ($show_sql) {
				$sql = $query->getSQL();
				echo "SQL: " . $sql;
			}
			
			$lista = $query->getResult();
		}
		
		$baseUrl = '/' . strtolower($Entity) . ($metodoPaginacao == '' ? '/index' : $metodoPaginacao);
		
		$paginacao = new Pagination($pg, $total_registros, $por_pagaina, 5, $baseUrl);
		$links = $paginacao->criarLinks();
		
		return ['lista' => $lista, 'paginacao' => $links, 'total_registros' => $total_registros];
	}
	
	public function filtroPost($classe, &$post)
	{
		if (isset($post['index']) && $post['index'] == '1') {
			session()->remove('dados_filtro_' . $classe);
		}
		unset($post['index']);
		if (isset($post['tipo_janela'])) {
			$post['tipo_janela'] = null;
			unset($post['tipo_janela']);
		}
		if (isset($post['nid'])) {
			$post['nid'] = null;
			unset($post['nid']);
		}
		
		//inicializando e gravando uma session dos filtros de busca
		if (count($post) >= 1) {
			session()->set('dados_filtro_' . $classe, $post);
		} else {
			if (is_array(session()->get('dados_filtro_' . $classe))) {
				$post = session()->get('dados_filtro_' . $classe);
			}
		}
	}
	
	public function abrePagina($pagina, $div = "container", $uid = "")
	{
		echo "<script type='text/javascript'>";
		/*if($div!="principal")
			echo "$('#jusModal_$uid').modal('hide');";
		else*/
		echo "abrir_div('$pagina','$div');";
		echo "</script>";
	}
	
	public function auditoriaDados($id = 0, $tabela = "", $rev = 0, $chave = "id")
	{
		$data['id'] = $id;
		$data['rev'] = $rev;
		
		//se $rev for vazio entao traz a ultima auditoria
		if ($rev != 0) {
			$rev = "AND c.REV <= " . $rev;
		} else {
			$rev = "";
		}
		
		$query1 = "SELECT
					c.*,
					DATE_FORMAT(FROM_UNIXTIME(SUBSTR(r.`timestamp`,1,10)), '%d/%m/%Y %H:%m') AS DATA,
					r.`login`
				FROM
					" . strtolower($tabela) . "_aud c
				INNER JOIN `revisao_auditoria` r ON r.`id` = c.`REV`
				WHERE
					c." . $chave . " = " . $id . "
					$rev
				ORDER BY c.REV DESC
				LIMIT 0,2";
		
		$registros = $this->dQuery($query1);
		$data['atual'] = [];
		$data['anterior'] = [];
		if (count($registros) == 2) {
			unset($registros[0]['REV']);
			unset($registros[1]['REV']);
			unset($registros[0]['DATA']);
			unset($registros[1]['DATA']);
			unset($registros[0]['login']);
			unset($registros[1]['login']);
			
			$diff = array_diff_assoc($registros[0], $registros[1]);
			if (count($diff) == 0) {
				$diff = array_diff_assoc($registros[1], $registros[0]);
			}
			
			foreach ($diff as $key => $value) {
				$k = str_ireplace('id', '', $key);
				$data['anterior'][$k] = $registros[1][$key];
				$data['atual'][$k] = $registros[0][$key];
			}
		}
		
		//se $rev for vazio entao monta a ultima auditoria
		if ($rev == '') {
			$tabela = "";
			foreach ($data['atual'] as $key => $value) {
				$tabela .= $key . " = " . ($data['anterior'][$key] != '' ? $data['anterior'][$key] : '"&nbsp;"') . " --> " . ($value != '' ? $value : '"&nbsp;"') . "<br>\n";
			}
			if (count($data['atual']) > 0) {
				return $tabela;
			}
			
			return "";
			
		}
		
		view('auditoria/auditoriadados_view', $data);
	}
	
	public function save($Entity, $post, $chave = "id", $auditar = false)
	{
		$doctrine = new Doctrine();
		$auditoria = model('AuditoriaModel');
		$this->load->model("auditoria");
		$tabela = $Entity;
		if (isset($post[$chave]) && $post[$chave] != '' && is_numeric($post[$chave])) {
			$Entity = $doctrine->em->getRepository("\App\Entity\\" . $Entity)->findOneBy([$chave => $post[$chave]]);
			
		} else {
			eval("\$Entity = new \App\Entity\\" . $Entity . ";");
		}
		
		foreach ($post as $key => $val) {
			$Entity->$key = $post[$key];
		}
		
		$doctrine->em->getConnection()->beginTransaction();
		try {
			
			$doctrine->em->persist($Entity);
			$doctrine->em->flush();
			
			if (false) {
				//serializando entidade para gravar na tabela de log
				require_once str_ireplace("system", "", BASEPATH) . "application/controllers/serializer.php";
				$serializer = new EntitySerializer($doctrine->em); // Pass the EntityManager object
				$array = $serializer->serialize($Entity); // Returns the array (with associations!)
				//caso no array não tem a chave cria-se apartir do post
				if (!isset($array[1][$chave])) $array[1][$chave] = $post[$chave];
				
				if (isset($array[1]['idColecaoSituacoes'])) unset($array[1]['idColecaoSituacoes']);
				$auditoria->gravar($tabela, $array[1], $revtype, $this->dados_usuario_logado['login']);
			}
			$doctrine->em->getConnection()->commit();
			return $Entity;
		} catch (\Exception $err) {
			$doctrine->em->getConnection()->rollBack();
			log_message("error", $err->getMessage());
			echo $err->getMessage();
			alerta("ERRO: " . addslashes($err->getMessage()));
			return false;
		}
	}
	
	public function showSQL()
	{
		$logger = new \Doctrine\DBAL\Logging\DebugStack();
		$this->doctrine->em->getConnection()->getConfiguration()->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
	}
	
	public function enviarEmailAnexo($email_remetente = "", $nome_remetente = "", $para = "", $assunto = "", $mensagem = "", $anexos = [])
	{
		$this->load->library('email');
		$this->email->clear(true);
		foreach ($anexos as $anexo) {
			$this->email->attach($anexo);
		}
		
		$this->email->from($email_remetente, $nome_remetente);
		$this->email->to($para);
		$this->email->subject($assunto);
		$this->email->message($mensagem);
		try {
			$envio = $this->email->send();
			//echo $this->email->print_debugger();
		} catch (Exception $e) {
			echo $e->getMessage();
			log_message("ERROR", "Envio de email: " . $e->getMessage());
			return false;
		}
		return $envio;
	}
	
	public function verificaPermissao()
	{
		if (!isset($this->dadosUsuarioLogado['permissao_acesso']) || !$this->dadosUsuarioLogado['permissao_acesso']) {
			echo "<script type='text/javascript'>top.location = '" . base_url('auth/logout') . "';</script>";
			exit;
		}
	}
}
