<?php

namespace App\Models;

use CI_Model;

class Auto extends CI_Model
{
	public function gerarSelect2($table, $param = [], $fields = [], $id = "", $name = "", $value = [], $title = "Digite uma Opção", $multiple = false, $order = "id desc", $class = "form-control", $attr = "")
	{
		$this->load->helper('html');
		
		$this->db->order_by($order);
		
		$this->db->select(implode(",", $fields));
		$this->db->from($table);
		
		if (count($param) > 0) {
			foreach ($param as $key => $val) {
				if (is_array($val) && count($val) > 0) {
					$this->db->where("$key in (" . implode(",", $val) . ")");
				} else {
					$this->db->where($key, $val);
				}
			}
		}
		
		$i = 0;
		
		$elements = [];
		$query = $this->db->get();
		foreach ($query->result() as $row) {
			
			$row = (array)$row;
			
			$elements[$i][$fields[0]] = $row[$fields[0]];
			
			$elements[$i][$fields[1]] = $row[$fields[1]];
			
			$i++;
			
		}
		
		
		return select2($elements, $id, $name, $value, $title, $multiple, $class, $attr);
	}
}

