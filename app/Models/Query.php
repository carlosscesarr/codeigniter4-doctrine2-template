<?php

namespace App\Models;

use CodeIgniter\Model;

class Query extends Model
{
	public function gerarResultadoSQL($sql = "", $showSQL = false)
	{
		if ($showSQL) {
			echo "<pre>$sql</pre>";
		}
		
		$query = db_connect()->query($sql);
		$elements = [];
		if ($query->getNumRows() == 0) {
			return $elements;
		}
		foreach ($query->getResultArray() as $row) {
			$elements[] = $row;
		}
		$query->freeResult();
		return $elements;
	}
}