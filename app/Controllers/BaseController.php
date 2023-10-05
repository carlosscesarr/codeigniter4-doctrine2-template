<?php

namespace App\Controllers;

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
	 * @return void
	 */
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);
		
		// Preload any models, libraries, etc, here.
		
		// E.g.: $this->session = \Config\Services::session();
	}
	
	public function dQuery($sql, $parametros = [], $showSQL = false)
	{
		$entity = new \App\Libraries\Doctrine();
		
		$conn = $entity->em->getConnection();
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
}
