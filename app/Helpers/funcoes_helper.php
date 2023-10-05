<?php

/**
 * Arrays do feriado do ano
 *
 * @access    public
 * @param string    ano
 * @return    array
 */
if (!function_exists('feriadosAno')) {
	
	function feriadosAno($ano = null)
	{
		if ($ano === null) {
			$ano = intval(date('Y'));
		}
		
		$pascoa = easter_date($ano); // Limite de 1970 ou após 2037 da easter_date PHP
		$dia_pascoa = date('j', $pascoa);
		$mes_pascoa = date('n', $pascoa);
		$ano_pascoa = date('Y', $pascoa);
		
		$feriados = [// Tatas Fixas dos feriados Nacionail Basileiras
			mktime(0, 0, 0, 1, 1, $ano), // Confraternização Universal - Lei nº 662, de 06/04/49
			mktime(0, 0, 0, 4, 21, $ano), // Tiradentes - Lei nº 662, de 06/04/49
			mktime(0, 0, 0, 5, 1, $ano), // Dia do Trabalhador - Lei nº 662, de 06/04/49
			mktime(0, 0, 0, 9, 7, $ano), // Dia da Independência - Lei nº 662, de 06/04/49
			mktime(0, 0, 0, 10, 12, $ano), // N. S. Aparecida - Lei nº 6802, de 30/06/80
			mktime(0, 0, 0, 11, 2, $ano), // Todos os santos - Lei nº 662, de 06/04/49
			mktime(0, 0, 0, 11, 15, $ano), // Proclamação da republica - Lei nº 662, de 06/04/49
			mktime(0, 0, 0, 12, 25, $ano), // Natal - Lei nº 662, de 06/04/49
			
			// These days have a date depending on easter
			mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 48, $ano_pascoa),//2ºferia Carnaval
			mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 47, $ano_pascoa),//3ºferia Carnaval
			mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 2, $ano_pascoa),//6ºfeira Santa
			mktime(0, 0, 0, $mes_pascoa, $dia_pascoa, $ano_pascoa),//Pascoa
			mktime(0, 0, 0, $mes_pascoa, $dia_pascoa + 60, $ano_pascoa),//Corpus Cirist
		];
		
		sort($feriados);
		foreach ($feriados as $key => $val) {
			$feriados[$key] = date("Y-m-d", $val);
		}
		$feriados[] = '2017-12-30'; #recesso dos bancos final de ano
		//$feriados[] = '2017-08-16'; #aniversario de teresina
		//$feriados[] = '2017-08-15'; #aniversario de fortaleza
		return $feriados;
	}
}

/**
 * Retorno se o dia e' util
 * @access    public
 * @param $date string
 * @return boolean
 */
if (!function_exists('isDiaUtil')) {
	function isDiaUtil($data)
	{
		$timestamp = strtotime($data);
		if (in_array($data, feriadosAno(date("Y", strtotime($data))))) return false;
		$dia = date('N', $timestamp);
		if ($dia >= 6) return false; else
			return true;
	}
}

/**
 * Retorna o proximo dia util a uma data
 * @access    public
 * @param date    date
 * @param string    saida
 * @return    string
 */
if (!function_exists('proximoDiaUtil')) {
	function proximoDiaUtil($data, $saida = 'Y-m-d')
	{
		$data = new DateTime($data);
		while (!isDiaUtil($data->format('Y-m-d'))) $data->modify('+1 day');
		
		return $data->format($saida);
	}
}

if (!function_exists('proximoDiaUtilV2')) {
	function proximoDiaUtilV2($data, $saida = 'Y-m-d')
	{
		$data = new DateTime($data);
		$data->modify('+1 day');
		while (!isDiaUtil($data->format('Y-m-d'))) $data->modify('+1 day');
		
		return $data->format($saida);
	}
}

if (!function_exists('data2input')) {
	function data2input($data, $saida = 'd/m/Y')
	{
		if ($data == '') return '';
		$data = new DateTime($data);
		return $data->format($saida);
	}
}

if (!function_exists('data2BD')) {
	function data2BD($data)
	{
		if (!validateDate($data)) {
			if ($data <> '') {
				$txt = explode("/", $data);
				if (count($txt) > 0) {
					return $txt[2] . "-" . $txt[1] . "-" . $txt[0];
				} else {
					return $data;
				}
			}
		} else {
			return $data;
		}
		return null;
	}
}

if (!function_exists('moeda2Input')) {
	function moeda2Input($num)
	{
		//if($num!=''&&is_numeric($num)){
		if (is_numeric($num)) {
			$num = number_format($num, 2, ',', '.');
		}
		return $num;
	}
}

if (!function_exists('moeda2BD')) {
	function moeda2BD($num)
	{
		if ($num != '') {
			$num = str_replace(".", "", $num);
			$num = str_replace(",", ".", $num);
		} else $num = 0;
		return $num;
	}
}

if (!function_exists('echo_memory_usage')) {
	function echo_memory_usage()
	{
		$mem_usage = memory_get_usage(true);
		if ($mem_usage < 1024) return $mem_usage . " bytes"; elseif ($mem_usage < 1048576) return round($mem_usage / 1024, 2) . " kilobytes";
		else
			return round($mem_usage / 1048576, 2) . " megabytes";
	}
}

if (!function_exists('validateDate')) {
	function validateDate($date, $format = 'Y-m-d')
	{
		if (is_array($date)) return false;
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}
}
/*
diferenca em dias
*/
if (!function_exists('dateDiff')) {
	function dateDiff($date1, $date2)
	{
		$time = new DateTime($date1);
		$diff = $time->diff(new DateTime($date2), true);
		return $diff->days;
	}
}

/*
diferenca em dias positivo ou negativo
*/
if (!function_exists('dateDiff2')) {
	function dateDiff2($date1, $date2)
	{
		$data_inicial = $date1;
		$data_final = $date2;
		$time_inicial = strtotime($data_inicial);
		$time_final = strtotime($data_final);
		$diferenca = $time_final - $time_inicial;
		$dias = floor($diferenca / (60 * 60 * 24));
		return $dias;
	}
}

/*
diferenca em dias
*/
if (!function_exists('dateDiff3')) {
	function dateDiff3($date1, $date2)
	{
		$time = new DateTime($date1);
		$diff = $time->diff(new DateTime($date2), true);
		return $diff;
	}
}

/*
diferenca em meses
*/
if (!function_exists('dateDiffMeses')) {
	function dateDiffMeses($date1, $date2)
	{
		$time = new DateTime($date1);
		$diff = $time->diff(new DateTime($date2), true);
		return ($diff->y * 12) + $diff->m;
	}
}

/*
diferenca em dias positivo ou negativo
*/
function dias_uteis($datainicial, $datafinal = null)
{
	if (!isset($datainicial)) return false;
	$segundos_datainicial = strtotime(str_replace("/", "-", $datainicial));
	if (!isset($datafinal)) $segundos_datafinal = time(); else $segundos_datafinal = strtotime(str_replace("/", "-", $datafinal));
	$dias = abs(floor(floor(($segundos_datafinal - $segundos_datainicial) / 3600) / 24));
	$uteis = 0;
	for ($i = 1; $i <= $dias; $i++) {
		$diai = $segundos_datainicial + ($i * 3600 * 24);
		$w = date('w', $diai);
		if ($w > 0 && $w < 6) {
			$uteis++;
		}
	}
	return $uteis;
}

if (!function_exists('add_date')) {
	function add_date($givendate, $day = 0, $mth = 0, $yr = 0)
	{
		$cd = strtotime($givendate);
		$newdate = date('Y-m-d', mktime(date('h', $cd), date('i', $cd), date('s', $cd), date('m', $cd) + $mth, date('d', $cd) + $day, date('Y', $cd) + $yr));
		return $newdate;
	}
}

if (!function_exists('getUltimoDiaMes')) {
	function getUltimoDiaMes($date)
	{
		$mes = date('m', strtotime($date));
		$ano = date('Y', strtotime($date));
		return "$ano-$mes-" . cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
	}
}

if (!function_exists('mask')) {
	function mask($val, $mask)
	{
		$maskared = '';
		$k = 0;
		for ($i = 0; $i <= strlen($mask) - 1; $i++) {
			if ($mask[$i] == '#') {
				if (isset($val[$k])) $maskared .= $val[$k++];
			} else {
				if (isset($mask[$i])) $maskared .= $mask[$i];
			}
		}
		return $maskared;
	}
}

/*
 * This "pre" print objects whitout infinity recursive.
 * */
if (!function_exists('pre')) {
	function pre($obj)
	{
		echo "<pre>";
		print_r(json_decode(json_encode($obj)));
		echo "</pre>";
	}
}

if (!function_exists('linkOperadoraFone')) {
	function linkOperadoraFone($numero)
	{
		if ($numero != '') {
			$numero = str_replace(" ", "", $numero);
			return '<a href="http://' . $_SERVER['HTTP_HOST'] . '/migrar/consutaloperadora/' . $numero . '" class="glyphicon glyphicon-earphone" target="_blank">&nbsp;</a>';
		}
		return "";
	}
}

if (!function_exists('validarEmail')) {
	function validarEmail($email_a)
	{
		if (filter_var($email_a, FILTER_VALIDATE_EMAIL)) {
			return true;
		}
		return false;
	}
}

if (!function_exists('alerta')) {
	function alerta($msn, $ok = false, $div = 'topo')
	{
		echo "<script type='text/javascript'>";
		if ($ok) {
			echo "alerta('$msn',true,'$div');";
		} else {
			echo "alerta('$msn',false,'$div');";
		}
		echo "</script>";
	}
}

if (!function_exists('verificaCPF')) {
	function verificaCPF($cpf)
	{
		
		if (preg_match('/^[0-9]{3,3}([.]?[0-9]{3,3})([.]?[0-9]{3,3})([-]?[0-9]{2,2})?$/', $cpf)) {
			$cpf = preg_replace("/[^0-9]/", "", $cpf);
			$digitoUm = 0;
			$digitoDois = 0;
			for ($i = 0, $x = 10; $i <= 8; $i++, $x--) {
				$digitoUm += $cpf[$i] * $x;
			}
			for ($i = 0, $x = 11; $i <= 9; $i++, $x--) {
				if (str_repeat($i, 11) == $cpf) {
					return false;
				}
				$digitoDois += $cpf[$i] * $x;
			}
			$calculoUm = (($digitoUm % 11) < 2) ? 0 : 11 - ($digitoUm % 11);
			$calculoDois = (($digitoDois % 11) < 2) ? 0 : 11 - ($digitoDois % 11);
			if ($calculoUm <> $cpf[9] || $calculoDois <> $cpf[10]) {
				return false;
			}
			return true;
		} else {
			return false;
		}
		
	}
}

if (!function_exists('verificaCNPJ')) {
	function verificaCNPJ($cnpj)
	{
		
		if (preg_match('/^[0-9]{2}([.]?[0-9]{3})([.]?[0-9]{3})\/\d{4}([-]?[0-9]{2})$/', $cnpj)) {
			
			$cnpj = trim($cnpj);
			$cnpj = str_replace(".", "", $cnpj);
			$cnpj = str_replace(",", "", $cnpj);
			$cnpj = str_replace("-", "", $cnpj);
			$cnpj = str_replace("/", "", $cnpj);
			
			if (strlen($cnpj) <> 14) return 0;
			
			$soma1 = ($cnpj[0] * 5) + ($cnpj[1] * 4) + ($cnpj[2] * 3) + ($cnpj[3] * 2) + ($cnpj[4] * 9) + ($cnpj[5] * 8) + ($cnpj[6] * 7) + ($cnpj[7] * 6) + ($cnpj[8] * 5) + ($cnpj[9] * 4) + ($cnpj[10] * 3) + ($cnpj[11] * 2);
			$resto = $soma1 % 11;
			$digito1 = $resto < 2 ? 0 : 11 - $resto;
			$soma2 = ($cnpj[0] * 6) + ($cnpj[1] * 5) + ($cnpj[2] * 4) + ($cnpj[3] * 3) + ($cnpj[4] * 2) + ($cnpj[5] * 9) + ($cnpj[6] * 8) + ($cnpj[7] * 7) + ($cnpj[8] * 6) + ($cnpj[9] * 5) + ($cnpj[10] * 4) + ($cnpj[11] * 3) + ($cnpj[12] * 2);
			$resto = $soma2 % 11;
			$digito2 = $resto < 2 ? 0 : 11 - $resto;
			return (($cnpj[12] == $digito1) && ($cnpj[13] == $digito2));
		} else {
			return 0;
		}
	}
}

if (!function_exists('_dateBetween')) {
	function _dateBetween($dataInicial, $dataFinal, $dataComparada)
	{
		if ($dataInicial == null || $dataFinal == null || $dataComparada == null) {
			return false;
		}
		
		$maiorIgual = compareData($dataComparada, $dataInicial) >= 0;
		$menorIgual = compareData($dataComparada, $dataFinal) <= 0;
		return $maiorIgual && $menorIgual;
	}
}

if (!function_exists('compareData')) {
	function compareData($data1, $data2)
	{
		$ano1 = @date("Y", strtotime($data1));
		$ano2 = @date("Y", strtotime($data2));
		$mes1 = @date("m", strtotime($data1));
		$mes2 = @date("m", strtotime($data2));
		$dia1 = @date("d", strtotime($data1));
		$dia2 = @date("d", strtotime($data2));
		
		if ($ano1 == $ano2) { // Se os anos são iguais, verificamos os meses
			if ($mes1 == $mes2) { // Se os meses são iguais, verificamos os dias
				if ($dia1 == $dia2) {
					return 0;
				} // Se os dias são iguais, as datas são iguais
				else if ($dia1 > $dia2) {
					return 1;
				} // Se dia1 maior que dia2, data1 vem apos data2
				else {
					return -1;
				} // Caso contrário, data2 vem apos data1
			} else if ($mes1 > $mes2) { // Se $mes1 é maior que mes2, então data1 vem apos data2
				return 1;
			} else {
				return -1;
			} // Caso contrário, data2 vem apos data1
		} else if ($ano1 > $ano2) {
			return 2;
		} else { // Se ano1 é maior que ano2, então data1 vem apos data2
			return -1;
		} // Caso contrário, data2 vem apos data1
	}
}
if (!function_exists('abrePagina')) {
	function abrePagina($pagina, $div = "container", $uid = "")
	{
		echo "<script type='text/javascript'>";
		echo "abrir_div('$pagina','$div');";
		echo "</script>";
	}
}

// a function for comparing two float numbers
// float 1 - The first number
// float 2 - The number to compare against the first
// operator - The operator. Valid options are =, <=, <, >=, >, <>, eq, lt, lte, gt, gte, ne
if (!function_exists('compareFloatNumbers')) {
	function compareFloatNumbers($float1, $float2, $operator = '=')
	{
		// Check numbers to 5 digits of precision
		$epsilon = 0.00001;
		
		$float1 = (float)$float1;
		$float2 = (float)$float2;
		
		switch ($operator) {
			// equal
			case "=":
			case "eq":
			{
				if (abs($float1 - $float2) < $epsilon) {
					return true;
				}
				break;
			}
			// less than
			case "<":
			case "lt":
			{
				if (abs($float1 - $float2) < $epsilon) {
					return false;
				} else {
					if ($float1 < $float2) {
						return true;
					}
				}
				break;
			}
			// less than or equal
			case "<=":
			case "lte":
			{
				if (compareFloatNumbers($float1, $float2, '<') || compareFloatNumbers($float1, $float2, '=')) {
					return true;
				}
				break;
			}
			// greater than
			case ">":
			case "gt":
			{
				if (abs($float1 - $float2) < $epsilon) {
					return false;
				} else {
					if ($float1 > $float2) {
						return true;
					}
				}
				break;
			}
			// greater than or equal
			case ">=":
			case "gte":
			{
				if (compareFloatNumbers($float1, $float2, '>') || compareFloatNumbers($float1, $float2, '=')) {
					return true;
				}
				break;
			}
			case "<>":
			case "!=":
			case "ne":
			{
				if (abs($float1 - $float2) > $epsilon) {
					return true;
				}
				break;
			}
			default:
			{
				die("Unknown operator '" . $operator . "' in compareFloatNumbers()");
			}
		}
		
		return false;
	}
}
if (!function_exists('adiciona_9_digito')) {
	function adiciona_9_digito($tel)
	{
		//verificando se é celular
		$array_pre_numero = ["9", "8", "7"];
		// retirando espaços
		$tel = trim($tel);
		$telefone = "";
		// seria melhor cirar uma white list.
		// tratando manualmente
		$tel = str_replace("-", "", $tel);
		$tel = str_replace("(", "", $tel);
		$tel = str_replace(")", "", $tel);
		$tel = str_replace("_", "", $tel);
		$tel = str_replace(" ", "", $tel);
		$tel = str_replace(".", "", $tel);
		//---------------------
		
		$tamanho = strlen($tel);
		
		// if(substr($tel,0,2)==61){
		// 	return $tel;
		// }
		
		// maior
		if ($tamanho > '10') {
			// não faz nada
			$telefone = $tel;
		}
		//igual
		if ($tamanho == '10') {
			$verificando_celular = substr($tel, 2, 1);
			if (in_array($verificando_celular, $array_pre_numero)) {
				$telefone .= substr($tel, 0, 2);
				$telefone .= "9"; // nono digito
				$telefone .= substr($tel, 2);
			} else {
				$telefone = $tel;
			}
		}
		//menor
		if ($tamanho < '10') {
			// não faz nada
			$telefone = $tel;
		}
		if ($tamanho == '8') {
			// não faz nada
			$telefone = adiciona_9_digito("86" . $tel);
		}
		
		return $telefone;
	}
}

if (!function_exists('removeDeletaArquivo')) {
	function removeDeletaArquivo($file)
	{
		try {
			unlink($file);
		} catch (Exception $e) {
			log_message("error", 'Erro ao apagar arquivo ' . $e->getMessage());
		}
	}
}

if (!function_exists('selectDatas')) {
	function selectDatas($id = "", $name = "", $values = [], $title = "Digite um Data", $obrigatorio = true, $multiple = false, $_str = "", $class = "")
	{
		$ci =& get_instance();
		$elements = $ci->config->item('datasClassecon');
		$str = "<select $_str id=\"$id\" name=\"$name\" title=\"$title\" " . ($obrigatorio ? 'required' : '') . " class=\"input-select2 form-control $class\" " . ($multiple ? 'multiple' : '') . ">";
		$str .= "<option value=\"\">$title</option>";
		foreach ($elements as $key => $val) {
			$str .= "<option " . (in_array($key, $values) || in_array($val, $values) ? 'selected' : '') . " value=\"" . $key . "\">" . str_pad($val, 2, '0', STR_PAD_LEFT) . "</option>";
		}
		$str .= "</select><script>exibe_select2('$id');</script>";
		return $str;
	}
}

/**
 * Library's ordenacao.
 *
 * @access    public
 * @param array    the URL
 * @param string    coluna para ordenar
 * @return    string    ordem
 */
if (!function_exists('array_sort_by_column')) {
	function array_sort_by_column(&$arr, $col, $dir = 'desc')
	{
		
		if (strtolower($dir) == 'desc') $dir = SORT_DESC; else
			$dir = SORT_ASC;
		
		$sort_col = [];
		foreach ($arr as $key => $row) {
			$sort_col[$key] = $row[$col];
		}
		
		return array_multisort($sort_col, $dir, $arr);
	}
}
if (!function_exists('modulo_10ItauListagem')) {
	function modulo_10ItauListagem($num)
	{
		$numtotal10 = 0;
		$fator = 2;
		
		// Separacao dos numeros
		for ($i = strlen($num); $i > 0; $i--) {
			// pega cada numero isoladamente
			$numeros[$i] = substr($num, $i - 1, 1);
			// Efetua multiplicacao do numero pelo (falor 10)
			// 2002-07-07 01:33:34 Macete para adequar ao Mod10 do Itaú
			$temp = $numeros[$i] * $fator;
			$temp0 = 0;
			foreach (preg_split('//', $temp, -1, PREG_SPLIT_NO_EMPTY) as $k => $v) {
				$temp0 += $v;
			}
			$parcial10[$i] = $temp0; //$numeros[$i] * $fator;
			// monta sequencia para soma dos digitos no (modulo 10)
			$numtotal10 += $parcial10[$i];
			if ($fator == 2) {
				$fator = 1;
			} else {
				$fator = 2; // intercala fator de multiplicacao (modulo 10)
			}
		}
		
		// várias linhas removidas, vide função original
		// Calculo do modulo 10
		$resto = $numtotal10 % 10;
		$digito = 10 - $resto;
		if ($resto == 0) {
			$digito = 0;
		}
		
		return $digito;
	}
}
if (!function_exists('geraSenha')) {
	/**
	 * Função para gerar senhas aleatórias
	 * @param integer $tamanho Tamanho da senha a ser gerada
	 * @param boolean $maiusculas Se terá letras maiúsculas
	 * @param boolean $numeros Se terá números
	 * @param boolean $simbolos Se terá símbolos
	 * @return string A senha gerada
	 */
	function geraSenha($tamanho = 8, $maiusculas = true, $numeros = true)
	{
		$lmin = 'abcdefghijklmnopqrstuvwxyz';
		$lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$num = '1234567890';
		$retorno = '';
		$caracteres = '';
		$caracteres .= $lmin;
		if ($maiusculas) $caracteres .= $lmai;
		if ($numeros) $caracteres .= $num;
		$len = strlen($caracteres);
		for ($n = 1; $n <= $tamanho; $n++) {
			$rand = mt_rand(1, $len);
			$retorno .= $caracteres[$rand - 1];
		}
		return $retorno;
	}
}
if (!function_exists('hiddenString')) {
	/**
	 * Oculta parte de un string
	 * @param string $str Texto a ocultar
	 * @param integer $start Cuantos caracteres dejar sin ocultar al inicio
	 * @param integer $end Cuantos caracteres dejar sin ocultar al final
	 * @return string *@author Jodacame
	 */
	function hiddenString($str, $start = 1, $end = 1)
	{
		$len = strlen($str);
		return substr($str, 0, $start) . str_repeat('*', $len - ($start + $end)) . substr($str, $len - $end + 1, $end);
	}
}

if (!function_exists('valorPorExtenso')) {
	/**
	 * Oculta parte de un string
	 * @param double $valor
	 * @param bolean $bolExibirMoeda
	 * @param bolean $bolPalavraFeminina
	 * @return string *@author Ramon Dev
	 */
	function valorPorExtenso($valor = 0, $bolExibirMoeda = true, $bolPalavraFeminina = false)
	{
		//$valor = self::removerFormatacaoNumero( $valor );
		
		$singular = null;
		$plural = null;
		
		if ($bolExibirMoeda) {
			$singular = ["centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão"];
			$plural = ["centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões"];
		} else {
			$singular = ["", "", "mil", "milhão", "bilhão", "trilhão", "quatrilhão"];
			$plural = ["", "", "mil", "milhões", "bilhões", "trilhões", "quatrilhões"];
		}
		
		$c = ["", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos"];
		$d = ["", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa"];
		$d10 = ["dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove"];
		$u = ["", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove"];
		
		
		if ($bolPalavraFeminina) {
			if ($valor == 1) {
				$u = ["", "uma", "duas", "três", "quatro", "cinco", "seis", "sete", "oito", "nove"];
			} else {
				$u = ["", "um", "duas", "três", "quatro", "cinco", "seis", "sete", "oito", "nove"];
			}
			$c = ["", "cem", "duzentas", "trezentas", "quatrocentas", "quinhentas", "seiscentas", "setecentas", "oitocentas", "novecentas"];
		}
		
		$z = 0;
		
		$valor = number_format($valor, 2, ".", ".");
		$inteiro = explode(".", $valor);
		
		for ($i = 0; $i < count($inteiro); $i++) {
			for ($ii = mb_strlen($inteiro[$i]); $ii < 3; $ii++) {
				$inteiro[$i] = "0" . $inteiro[$i];
			}
		}
		
		// $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
		$rt = null;
		$fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
		for ($i = 0; $i < count($inteiro); $i++) {
			$valor = $inteiro[$i];
			$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
			$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
			$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";
			
			$r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
			$t = count($inteiro) - 1 - $i;
			$r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
			if ($valor == "000") {
				$z++;
			} elseif ($z > 0) {
				$z--;
			}
			
			if (($t == 1) && ($z > 0) && ($inteiro[0] > 0)) {
				$r .= (($z > 1) ? " de " : "") . $plural[$t];
			}
			
			if ($r) {
				$rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? (($i < $fim) ? ", " : " e ") : " ") . $r;
			}
		}
		
		$rt = mb_substr($rt, 1);
		
		return ($rt ? trim($rt) : "zero");
	}
}

if (!function_exists('getGeoLocalAddress')) {
	function getGeoLocalAddress($address, $saida = false)
	{
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&key=AIzaSyC08LVAhLItQ0gagUocoIXyZ7m86ZZmINg';
		//echo $url;
		$json = @file_get_contents($url);
		$data = json_decode($json);
		$status = isset($data->status) ? $data->status : 'erro';
		if ($status == "OK") {
			if ($saida) {
				pre($data);
			}
			return ['geolocal' => $data->results[0]->geometry->location, 'ok' => true];
		} else {
			return ['ok' => false, 'status' => $status];
		}
	}
}

if (!function_exists('limparDocumento')) {
	function limparDocumento($documento = 0)
	{
		$documento = str_replace("/", "", $documento);
		$documento = str_replace(".", "", $documento);
		$documento = str_replace("-", "", $documento);
		$documento = trim($documento);
		return $documento;
	}
}

if (!function_exists('dataExtenso')) {
	function dataExtenso($date)
	{
		$dia = date('d', strtotime($date));
		$mes = date('m', strtotime($date));
		$ano = date('Y', strtotime($date));
		
		$d1 = ['', 'décimo', 'vigésimo', 'trigésimo'];
		$d2 = ['', 'primeiro', 'segundo', 'terceiro', 'quarto', 'quinto', 'sexto', 'sétimo', 'oitavo', 'nono'];
		
		$m1 = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
		
		$a1 = ['', 'mil', 'dois mil', 'tres mil', 'quatro mil'];
		$a2 = ['', 'cento', 'duzentos', 'trezentos', 'quatrocentos', 'quinhentos', 'seiscentos', 'setecentos', 'oitocentos', 'novecentos'];
		$a3 = ["", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa"];
		$a4 = ["", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove"];
		$a5 = ["", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove"];
		
		// retorna o dia por extenso
		$diaE = str_split($dia);
		$newD = $d1[$diaE[0]];
		$newD .= " " . $d2[$diaE[1]];
		
		// retorna o mês por extenso
		$newM = $m1[$mes - 1];
		
		// retorna o ano por extenso
		$anoE = str_split($ano);
		$newA = $a1[$anoE[0]];
		$newA .= $anoE[1] == 1 && $anoE[2] == 0 && $anoE[3] == 0 ? ' e cem' : ' ' . $a2[$anoE[1]];
		$newA .= $anoE[2] == 1 && $anoE[3] == 0 ? ' e ' . $a3[$anoE[2]] : ($anoE[2] >= 2 ? ' e ' . $a3[$anoE[2]] : '');
		$newA .= $anoE[2] == 1 && $anoE[3] >= 1 ? ' e ' . $a4[$anoE[3]] : '';
		$newA .= ($anoE[2] == 0 && $anoE[3] >= 1) || ($anoE[2] >= 2 && $anoE[3] >= 1) ? ' e ' . $a5[$anoE[3]] : '';
		
		$dataExtenso = $newD . ' dia do mês de ' . $newM . ' do ano de ' . $newA;
		return preg_replace('/\s+/', ' ', $dataExtenso);
	}
}

if (!function_exists('mesExtenso')) {
	function mesExtenso($data)
	{
		$dia = date('d', strtotime($data));
		$mes = date('m', strtotime($data));
		$ano = date('Y', strtotime($data));
		
		$mesExtenso = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
		
		$dataExtenso = $dia . " de " . $mesExtenso[$mes - 1] . " de " . $ano;
		return $dataExtenso;
	}
}

// em construção
if (!function_exists('numeroExtenso')) {
	function numeroExtenso($numero, $tipo)
	{
		//numero: 1,2,3,4... | tipo: c (cardinal) / o (ordinal)
		
		if ($tipo == 'o') {
			$n1 = ['', 'décimo', 'vigésimo', 'trigésimo', 'quadragésimo', 'quinquagésimo', 'sexagésimo', 'septuagésimo', 'octogésimo', 'nonagésimo'];
			$n2 = ['', 'primeiro', 'segundo', 'terceiro', 'quarto', 'quinto', 'sexto', 'sétimo', 'oitavo', 'nono'];
		} else {
			$n1 = ['', 'cento', 'duzentos', 'trezentos', 'quatrocentos', 'quinhentos', 'seiscentos', 'setecentos', 'oitocentos', 'novecentos'];
			$n2 = ["", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa"];
			$n3 = ["", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove"];
			$n4 = ["", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove"];
		}
		// retorna o ano por extenso
		$numeroE = str_split($numero);
		$newN = $numeroE[0] == 1 && $numeroE[1] >= 1 ? ' e ' . $n3[$numeroE[1]] : '';
		$newN .= ($numeroE[0] == 0 && $numeroE[1] >= 1) || ($numeroE[0] >= 2 && $numeroE[1] >= 1) ? ' e ' . $n4[$numeroE[3]] : '';
		
		$numeroExtenso = $newN;
		return preg_replace('/\s+/', ' ', $numeroExtenso);
	}
}

if (!function_exists('checkDispositivoMobile')) {
	function checkDispositivoMobile()
	{
		$iphone = strpos($_SERVER['HTTP_USER_AGENT'], "iPhone");
		$ipad = strpos($_SERVER['HTTP_USER_AGENT'], "iPad");
		$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");
		$palmpre = strpos($_SERVER['HTTP_USER_AGENT'], "webOS");
		$berry = strpos($_SERVER['HTTP_USER_AGENT'], "BlackBerry");
		$ipod = strpos($_SERVER['HTTP_USER_AGENT'], "iPod");
		$symbian = strpos($_SERVER['HTTP_USER_AGENT'], "Symbian");
		$windowsphone = strpos($_SERVER['HTTP_USER_AGENT'], "Windows Phone");
		$expo = strpos($_SERVER['HTTP_USER_AGENT'], "Expo");
		
		if ($iphone || $ipad || $android || $palmpre || $ipod || $berry || $symbian || $windowsphone || $expo == true) {
			return true;
		} else {
			return false;
		}
		
	}
}

if (!function_exists('LimparCamposVazios')) {
	function LimpaCamposVazios(array $arrayCampos)
	{
		$filtro = array_filter($arrayCampos);
		$dif = array_values($filtro);
		
		return $dif;
	}
}

/* End of file array_helper.php */
/* Location: ./system/helpers/array_helper.php */

if (!function_exists('adicionarHoras')) {
	function adicionarHoras($hora, $qtdhoras, $qtdminutos, $bolRetornaSegundos = false)
	{
		
		$vetor = explode(":", $hora);
		
		$hora = $vetor[0];
		$minuto = $vetor[1];
		$segundo = (count($vetor) == 3) ? $vetor[2] : 0;
		
		if ($qtdhoras != "00") {
			$hora += $qtdhoras;
		}
		
		if ($qtdminutos != "00") {
			
			$minuto += $qtdminutos;
			
			if ($minuto > 59) {
				$minuto = $minuto - 60;
				$hora = $hora + 1;
			}
			
		}
		
		$montaData = $hora . ":" . $minuto;
		
		return $montaData;
		
		
	}
	
}

if (!function_exists('sanitizaSringParaTabelaLatin1')) {
	function sanitizaSringParaTabelaLatin1($string)
	{
		/*
		Explicação:
		converter a string de utf-8 para iso-8859-1
		retorne para utf-8 (função mb_ substitui caracteres inválidos por ''?''remove caracteres inválidos)
		Substituir ? para ninguém
		Retorna o caractere ''?'' da string original
		Certifique-se de estar usando UTF-8 para funcionar.
		*/
		
		$string = str_replace('?', '{%}', $string);
		$string = mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
		$string = mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
		$string = str_replace(['?', '? ', ' ?'], [''], $string);
		$string = str_replace('{%}', '?', $string);
		return trim($string);
	}
}

if (!function_exists('removeEmojis')) {
	/**
	 * Função responsável por remover caracteres emoji de uma string
	 */
	function removeEmoji($string)
	{
		
		// Match Emoticons
		$regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
		$clear_string = preg_replace($regex_emoticons, '', $string);
		
		// Match Miscellaneous Symbols and Pictographs
		$regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
		$clear_string = preg_replace($regex_symbols, '', $clear_string);
		
		// Match Transport And Map Symbols
		$regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
		$clear_string = preg_replace($regex_transport, '', $clear_string);
		
		// Match Miscellaneous Symbols
		$regex_misc = '/[\x{2600}-\x{26FF}]/u';
		$clear_string = preg_replace($regex_misc, '', $clear_string);
		
		// Match Dingbats
		$regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
		$clear_string = preg_replace($regex_dingbats, '', $clear_string);
		
		return $clear_string;
	}
}

if (!function_exists('strposArray')) {
	/**
	 * Referência da função: https://www.php.net/manual/pt_BR/function.strpos.php
	 * @param $string string
	 * @param $filtro string || array
	 */
	function strposArray($string, $filtro)
	{
		if (is_array($filtro)) {
			if (count($filtro) == 0) {
				return false;
			}
			
			foreach ($filtro as $str) {
				if (is_array($str)) {
					$pos = strposArray($string, $str);
				} else {
					$pos = strpos($string, $str);
				}
				
				if ($pos !== false) {
					return $pos;
				}
			}
		} else {
			return strpos($string, $filtro);
		}
	}
}

if (!function_exists('sanitizaStringParaTabelaLatin1')) {
	/**
	 * Referência da função: https://stackoverflow.com/questions/12807176/php-writing-a-simple-removeemoji-function
	 * @param $string string
	 * @param string
	 */
	function sanitizaStringParaTabelaLatin1($string = '')
	{
		/*
		Explicação:
		converter a string de utf-8 para iso-8859-1
		retorne para utf-8 (função mb_ substitui caracteres inválidos por ''?''remove caracteres inválidos)
		Substituir ? para ninguém
		Retorna o caractere ''?'' da string original
		Certifique-se de estar usando UTF-8 para funcionar.
		*/
		
		$string = str_replace('?', '{%}', $string);
		$string = mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
		$string = mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
		$string = str_replace(['?', '? ', ' ?'], [''], $string);
		$string = str_replace('{%}', '?', $string);
		return trim($string);
	}
	
	if (!function_exists('removeCaracteresEspeciais')) {
		// Função para retirar acentos e alguns caracteres especiais de uma string
		function removeCaracteresEspeciais($string = '')
		{
			if ($string == '') return '';
			
			// isso
			$that = ['á', 'à', 'â', 'ã', 'Á', 'À', 'Â', 'Ã', 'é', 'è', 'ê', 'É', 'È', 'Ê', 'í', 'ì', 'î', 'Í', 'Ì', 'Î', 'ò', 'ó', 'ô', 'õ', 'Ó', 'Ò', 'Ô', 'Õ', 'ú', 'ù', 'û', 'Ú', 'Ù', 'Û', 'ç', 'Ç', 'Ñ', 'ñ', 'ä', 'ü', 'ö', 'ë', 'ï', 'Ä', 'Ö', 'Ü', 'Ë', 'Ï', 'ý', 'Ý', '[', ']', '>', '<', ':', ';', ',', '!', '?', '*', '%', '~', '^', "`", '@', '°', 'ª', 'º', "´", '¨', '+', '_', '$', '(', ')', '-', "'", '.', '|', '{', '}', '=', '/', '#', '&', '"', '¹', '²', '³', '£', '¢', '¬', '§', '?', "\\", "’"];
			// porIsso
			$thereFore = ['a', 'a', 'a', 'a', 'A', 'A', 'A', 'A', 'e', 'e', 'e', 'E', 'E', 'E', 'i', 'i', 'i', 'I', 'I', 'I', 'o', 'o', 'o', 'o', 'O', 'O', 'O', 'O', 'u', 'u', 'u', 'U', 'U', 'U', 'c', 'C', 'N', 'n', 'a', 'u', 'o', 'e', 'i', 'A', 'O', 'U', 'E', 'I', 'y', 'Y', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '1', '2', '3', '', '', '', '', '', '', ''];
			return str_replace($that, $thereFore, trim($string));
		}
	}
	
	if (!function_exists('removeEspacosDuplicados')) {
		function removeEspacosDuplicados($string = '')
		{
			return preg_replace('/( )+/', ' ', $string);
		}
	}
}