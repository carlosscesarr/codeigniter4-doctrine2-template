<?php


use CodeIgniter\Database\Config;
use Config\Database;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

// Create a simple "default" Doctrine ORM configuration for Annotations

/**
 * Class Doctrine
 */
class Doctrine
{
	static private $entitiesDir = [APPPATH . "Models\Entity"];
	static private $proxyDir = APPPATH . "Models\Proxies";
	static private $cache = null;
	
	static private $autoGerarProxies = true;
	static private $useSimpleAnnotationReader = false;
	
	/**
	 * @var EntityManager
	 */
	public $em;
	
	public function __construct() {
		// d(self::$proxyDir);
		$this->em = self::entityManageAttributeAnnotation();
	}
	
	private static function isDevMode() {
		return ENVIRONMENT === 'DEVELOPMENT';
	}
	public static function entityManageAttributeAnnotation(): EntityManager
	{
		
		if (self::isDevMode()) {
		    $queryCache = new ArrayAdapter();
		    $metadataCache = new ArrayAdapter();
		} else {
		    $queryCache = new PhpFilesAdapter('doctrine_queries');
		    $metadataCache = new PhpFilesAdapter('doctrine_metadata');
		}
		$config = new Configuration;
		$config->setMetadataCache($metadataCache);
		
		// Utilizar attributeDrive quando o tipo de anotação das entidades for por atributos #
		// $driverImpl = new AttributeDriver(self::$entitiesDir);
		
		// Utilizar AnnotationDriver quando o tipo de anotação das entidades for por anotações @
		$driverImpl = new AnnotationDriver(new AnnotationReader(), self::$entitiesDir);

		
		$config->setMetadataDriverImpl($driverImpl);
		$config->setQueryCache($queryCache);
		
		$config->setProxyDir(self::$proxyDir);
		$config->setProxyNamespace('App\Models\Proxies');
		
		if (self::isDevMode()) {
			$config->setAutoGenerateProxyClasses(self::$autoGerarProxies);
		} else {
			$config->setAutoGenerateProxyClasses(false);
		}
		
		$connection = DriverManager::getConnection(self::getConnectionOptions(), $config);
		
		return new EntityManager($connection, $config);
	}
	
	public static function entityManageAnnotation(): EntityManager
	{
		$config = ORMSetup::createAnnotationMetadataConfiguration(
		    paths: self::$entitiesDir,
		    isDevMode: self::isDevMode(),
			proxyDir: self::$proxyDir
		);
		$connection = DriverManager::getConnection(self::getConnectionOptions(), $config);
		
		return new EntityManager($connection, $config);
	}
	
	static function getConnectionOptions(): array
	{
		
		$configDatabase = new Database();
		return [
			'driver' => 'pdo_mysql',
	        'user' =>     $configDatabase->default['username'],
	        'password' => $configDatabase->default['password'],
	        'host' =>     $configDatabase->default['hostname'],
	        'dbname' =>   $configDatabase->default['database']
		];
	}
}