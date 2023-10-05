<?php

require_once __DIR__ . '/../vendor/autoload.php';

// use ;
use Config\Database;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

// Create a simple "default" Doctrine ORM configuration for Annotations

/**
 * Class Doctrine
 */
class Doctrine
{
	
	static public $isDevMode = true;
	static private $entitiesDir = [__DIR__ . "/../app/Models/Entity"];
	static private $proxyDir = __DIR__."/../app/Models/Proxies";
	static private $cache = null;
	
	static private $autoGerarProxies = true;
	/**
	 * @var EntityManager
	 */
	public $em;
	
	public function __construct()
	{
		$this->em = self::retrieveEntityManager2();
	}
	
	public static function retrieveEntityManager(): EntityManager
	{
		
		if (self::$isDevMode) {
			$queryCache = new ArrayAdapter();
			$metadataCache = new ArrayAdapter();
		} else {
		    $queryCache = new PhpFilesAdapter('doctrine_queries');
		    $metadataCache = new PhpFilesAdapter('doctrine_metadata');
		}
		
		$config = ORMSetup::createAttributeMetadataConfiguration(self::$entitiesDir, self::$isDevMode);
		$config->setMetadataCache($metadataCache);
		
		$driverImpl = new AttributeDriver(self::$entitiesDir);
		
		$config->setMetadataDriverImpl($driverImpl);
		$config->setQueryCache($queryCache);
		
		$config->setProxyDir(self::$proxyDir);
		$config->setProxyNamespace('App\Models\Proxies');
		
		if (self::$isDevMode) {
			$config->setAutoGenerateProxyClasses(self::$autoGerarProxies);
		} else {
			$config->setAutoGenerateProxyClasses(false);
		}
		
		$connection = DriverManager::getConnection(self::getConnectionOptions(), $config);
		
		return new EntityManager($connection, $config);
	}
	
	public static function retrieveEntityManager2(): EntityManager
	{
		
		$config = ORMSetup::createAttributeMetadataConfiguration(
		    paths: array(__DIR__ . "/../app/Models/Entity"),
		    isDevMode: true
		);
		$connection = DriverManager::getConnection(self::getConnectionOptions(), $config);
		
		return new EntityManager($connection, $config);
	}
	
	public static function getConnectionOptions(): array
	{
		$configDatabase = new Database();
		return [
			'driver'   => 'pdo_mysql',
			'user'     => $configDatabase->default['username'],
			'password' => $configDatabase->default['password'],
			'host'     => $configDatabase->default['hostname'],
			'dbname'   => $configDatabase->default['database']
		];
	}
	
	
}

// replace with path to your own project bootstrap file

// replace with mechanism to retrieve EntityManager in your app

$commands = [
    // If you want to add your own custom console commands,
    // you can do so here.
];

$doctrine = new Doctrine();

// replace with file to your own project bootstrap

// replace with mechanism to retrieve EntityManager in your app
$entityManager = $doctrine->em;
ConsoleRunner::run(
    new SingleManagerProvider($entityManager),
    $commands
);