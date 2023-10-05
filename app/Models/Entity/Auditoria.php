<?php

namespace Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;

/**
 * Auditoria
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class Auditoria
{
	public function __construct()
	{
	
	}
	
	/** @ORM\PostPersist */
	public function doSPostPersistIsert(LifecycleEventArgs $args)
	{
		$em = $args->getEntityManager();
		$tabela = str_ireplace("proxies\__cg__\\", "", str_ireplace("Entity\\", "", get_class($this)));
		$tabela = str_ireplace("doctrine", "", $tabela);
		log_message('debug', "Preparando Entidade $tabela para Auditoria-Inserir");
		
		$dados = $this->object_to_array($this, $em);
		$login = isset($_SESSION['dados_user_ci']['login']) ? $_SESSION['dados_user_ci']['login'] : '';
		
		$this->gravar($tabela, $dados, 0, $login, "", $em);
	}
	
	/** @PORM\ostUpdate */
	public function doSPostPersistUpdate(LifecycleEventArgs $args)
	{
		$em = $args->getEntityManager();
		$tabela = str_ireplace("proxies\__cg__\\", "", str_ireplace("Entity\\", "", get_class($this)));
		$tabela = str_ireplace("doctrine", "", $tabela);
		log_message('debug', "Preparando Entidade $tabela para Auditoria-Update");
		
		$dados = $this->object_to_array($this, $em);
		$login = isset($_SESSION['dados_user_ci']['login']) ? $_SESSION['dados_user_ci']['login'] : '';
		$this->gravar($tabela, $dados, 1, $login, "", $em);
	}
	
	/** @ORM\PreRemove */
	public function doSPostPersistRemove(LifecycleEventArgs $args)
	{
		$em = $args->getEntityManager();
		$tabela = str_ireplace("proxies\__cg__\\", "", str_ireplace("Entity\\", "", get_class($this)));
		$tabela = str_ireplace("doctrine", "", $tabela);
		log_message('debug', "Preparando Entidade $tabela para Auditoria-delete");
		
		$dados = $this->object_to_array($this, $em);
		$login = isset($_SESSION['dados_user_ci']['login']) ? $_SESSION['dados_user_ci']['login'] : '';
		$this->gravar($tabela, $dados, 2, $login, "", $em);
	}
	
	private function object_to_array($obj, $em)
	{
		
		$remover = ['__initializer__', '__isInitialized__', '__cloner__', 'Doctrine\DBAL\Connection'];
		$_arr = is_object($obj) ? get_object_vars($obj) : $obj;
		$arr = [];
		foreach ($_arr as $key => $val) {
			if (!in_array($key, $remover)) {
				if (is_object($val) && !($val instanceof \DateTime)) {
					//var_dump($val);
					if (get_class($val) != 'Doctrine\ORM\PersistentCollection' && get_class($val) != 'Doctrine\DBAL\Connection') {
						$val = $this->getEntityKeyValue($val, $em);
					}
				}
				//$val = (is_array($val) || is_object($val)) ? $this->object_to_array($val) : $val;
				if (!is_object($val)) {
					$arr[$key] = $val;
				}
				if ($val instanceof \DateTime) {
					$arr[$key] = $val->format("Y-m-d H:i:s");
				}
			}
		}
		return $arr;
	}
	
	private function getEntityKeyValue($entity, $em)
	{
		$meta = $em->getClassMetadata(get_class($entity));
		$key = $meta->getSingleIdentifierFieldName();
		return isset($entity->$key) ? $entity->$key : null;
	}
	
	private function gravar($tabela, $data, $revtype = 0, $login, $motivoAlteracao = "", $em)
	{
		if (isset($_SESSION['motivo']) && strlen($_SESSION['motivo']) > 3) {
			$motivoAlteracao = $_SESSION['motivo'];
			//unset($_SESSION['motivo']);
		}
		$conn = $em->getConnection();
		$REVTYPE1 = [0 => "CADASTRO", 1 => "ALTERACAO", 2 => "EXCLUSAO"];
		$tabelas_exceto = [];
		#previnindo se caso uma entidade so vir com 1 campo. nao fazer auditoria
		if (count($data) > 1) {
			foreach ($data as $key => $value) {
				if ($value instanceof DateTime) {
					$data[$key] = $value->format('Y-m-d H:i:s');
				}
			}
			#pre($tabela);
			//gerando revisao e retornando ID
			if (!in_array(strtolower($tabela), $tabelas_exceto)) {
				$rev_aud = ["timestamp" => strtotime(date("Y-m-d H:i:s")), "login" => $login, "motivoAlteracao" => $motivoAlteracao];
				
				$conn->executeUpdate('insert into revisao_auditoria (timestamp,login,motivoAlteracao, dataHora)values(?,?,?,?)', [$rev_aud['timestamp'], $rev_aud['login'], $rev_aud['motivoAlteracao'], date("Y-m-d H:i:s")]);
				$rev = $conn->lastInsertId();
				$data['REV'] = $rev;
				$data['REVTYPE'] = $revtype;
			} else {
				if (in_array(strtolower($tabela), $tabelas_exceto)) {
				}
			}
			
			$this->verificaTabelaAudExiste(strtolower($tabela), $conn);
			
			$insert = $this->gerarSqlInsert($data, strtolower($tabela) . '_aud');
			#log_message('debug',print_r($insert,1));
			$conn->executeUpdate($insert['sql'], $insert['valores']);
		} else {
			log_message('debug', "Entidade $tabela poucos campos (" . print_r($data, 1) . ")");
		}
	}
	
	private function verificaTabelaAudExiste($tabela, $conn)
	{
		$sql = "SHOW TABLES LIKE '" . $tabela . "_aud';";
		$stmt = $conn->query($sql);
		$gerado = false;
		while ($row = $stmt->fetch()) {
			$gerado = true;
		}
		
		if (!$gerado) {
			
			$stmt = $conn->query('SHOW FIELDS FROM ' . $tabela);
			while ($row = $stmt->fetch()) {
				//pre($row);
				$fields[] = $row['Field'] . ' ' . $row['Type'];
			}
			$sql_aud = 'CREATE TABLE IF NOT EXISTS ' . $tabela . '_aud (
                                              ' . implode(',', $fields) . ',
                                              `REV` int(11) NOT NULL,
                                              `REVTYPE` tinyint(4) DEFAULT NULL,
                                              PRIMARY KEY (id,REV),
                                              KEY idxrev' . $tabela . uniqid() . ' (REV),
                                              CONSTRAINT idxrev' . $tabela . uniqid() . ' FOREIGN KEY (REV) REFERENCES revisao_auditoria (id)
                                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1';
			//pre($sql_aud);
			$stmt = $conn->executeQuery($sql_aud);
		}
	}
	
	private function gerarSqlInsert($post, $tabela)
	{
		$colunas_bit = [];
		
		$campos = array_keys($post);
		
		$sql = "insert into $tabela (";
		
		for ($i = 0; $i < count($campos); $i++) {
			
			if ($i != count($campos) - 1) {
				$sql = $sql . $campos[$i] . ", ";
			} else {
				$sql = $sql . $campos[$i];
			}
			
		}
		
		$sql = $sql . ") values (";
		
		for ($i = 0; $i < count($campos); $i++) {
			$bit = "";
			if (in_array($campos[$i], $colunas_bit)) {
				$bit = "b";
				$post[$campos[$i]] = $post[$campos[$i]] == null ? 0 : $post[$campos[$i]];
			}
			if ($i != count($campos) - 1) {
				$sql = $sql . $bit . "?, ";
			} else {
				$sql = $sql . $bit . '?';
			}
		}
		$valores = [];
		for ($i = 0; $i < count($campos); $i++) {
			$valores[] = $post[$campos[$i]];
		}
		$sql = $sql . ")";
		return ['sql' => $sql, 'valores' => $valores];
	}
}
