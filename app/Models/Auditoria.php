<?php

namespace App\Models;

use CI_Model;

class Auditoria extends CI_Model
{

	var $REVTYPE = [0 => "CADASTRO", 1 => "ALTERACAO", 2 => "EXCLUSAO"];
	
	/**
	 * @param string $tabela nome da tabela
	 * @param array $data
	 * @param integer $revtype (0 - ADD, 1 - ALT, 2 - DEL)
	 * @param string $login login do usuario logado
	 * @param string $motivoAlteracao texto sobre o motivo da alteracao
	 */
	
	public function gravar($tabela, $data, $revtype = 0, $login, $motivoAlteracao = "")
	{
		
		
		//gerando revisao e retornando ID		
		
		$rev_aud = ["timestamp" => strtotime(date("Y-m-d H:i:s")),
		            
		            "login" => $login,
		            
		            "motivoAlteracao" => $motivoAlteracao];
		
		
		$this->db->insert('revisao_auditoria', $rev_aud);
		
		$rev = $this->db->insert_id();
		
		//print_r($data);
		
		//gerando a auditoria da tabela		
		
		$data['REVTYPE'] = $revtype;
		
		$data['REV'] = $rev;
		
		$this->db->insert(strtolower($tabela) . '_aud', $data);
		
		return $this->db->insert_id();
		
	}
	
	public function getAuditoriaId($id, $tabela, $chave = "id")
	{
		
		$tabela = strtolower($tabela);
		
		//select r.*,c.revtype from condomino_aud c, revisao_auditoria r where c.id = 39997 and r.id = c.REV
		
		
		$this->db->select("revisao_auditoria.timestamp,revisao_auditoria.login,revisao_auditoria.motivoAlteracao," . $tabela . "_aud.revtype," . $tabela . "_aud." . $chave . "," . $tabela . "_aud.rev");
		
		$this->db->join("revisao_auditoria", "revisao_auditoria.id = " . $tabela . "_aud.REV");
		
		$query = $this->db->get_where($tabela . "_aud", $tabela . "_aud.$chave = " . $id);
		
		$i = 0;
		
		$elements = [];
		
		//echo "<pre>";
		
		foreach ($query->result() as $row) {
			
			$row->data_hora = date("d/m/Y H:i:s", substr($row->timestamp, 0, 11));
			
			$row->operacao = $this->REVTYPE[$row->revtype];
			
			$elements[] = $row;
			
		}
		
		return $elements;
		
	}
	
}

