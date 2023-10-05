<?php


namespace App\Controllers;

use Doctrine;

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Tools\SchemaTool;

use Doctrine\DBAL\Connection,
    Doctrine\DBAL\Exception as DoctrineException;

use Exception;

class DoctrineTools extends BaseController {

    /**
     * Dctrine2 Entity Manager
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var  SchemaTool $schemaTool
     */
    protected $schemaTool;

    /**
     * DoctrineTools constructor.
     */
    public function __construct() {
        $this->em = Doctrine::retrieveEntityManager();
        $this->schemaTool = new SchemaTool($this->em);
        $connection = $this->em->getConnection();
        $this->registerCustomDatabaseTypes($connection);

    }

    public function index() {
        if(!$_POST) {
            die(view('layout/doctrine'));
        } else {
            try {
                $classes =  $this->getEntitiesList();
                $this->schemaTool->updateSchema($classes);
                die(view('layout/doctrine', array('success' => true)));
            } catch (Exception $ex){
                 throw new Exception($ex->getMessage());
            }
        }

    }

    private function getEntitiesList(): array{
        return  [
            $this->em->getClassMetadata('App\Models\Entity\Admin'),
        ];

    }
    private function registerCustomDatabaseTypes(Connection $connection) {
        try {
            $connection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        } catch (DoctrineException $ex) {
            throw DoctrineException::unknownColumnType('');
        }
    }
	
	public function gerarEntidadesDoBancoUtilizandoDoctrine()
	{
		$doctrine = new \App\Libraries\Doctrine();
		
		
		// replace with file to your own project bootstrap
		
		// replace with mechanism to retrieve EntityManager in your app
		$entityManager = $doctrine->em;
		// custom datatypes (not mapped for reverse engineering)
			$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('set', 'string');
			$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
			
			// fetch metadata
			$driver = new \Doctrine\ORM\Mapping\Driver\DatabaseDriver(
			                $entityManager->getConnection()->getSchemaManager()
			);
			$entityManager->getConfiguration()->setMetadataDriverImpl($driver);
			$cmf = new \Doctrine\ORM\Tools\DisconnectedClassMetadataFactory($entityManager);
			$cmf->setEntityManager($entityManager);
			$classes = $driver->getAllClassNames();
			$metadata = $cmf->getAllMetadata();
			$generator = new \Doctrine\ORM\Tools\EntityGenerator();
			$generator->setUpdateEntityIfExists(true);
			$generator->setGenerateStubMethods(true);
			$generator->setGenerateAnnotations(true);
			$generator->generate($metadata, __DIR__ . '/../../classes-geradas');
			print 'Done!';
	}
	
	public function gerarEntidadesDoBanco()
	{
		// return view('welcome_message');
		$doctrine = new \App\Libraries\Doctrine();
		
		
		// replace with file to your own project bootstrap
		
		// replace with mechanism to retrieve EntityManager in your app
		$entityManager = $doctrine->em;
		
		// return ConsoleRunner::createHelperSet($entityManager);
		// dd($doctrine);
		
		$pdo = new PDO("mysql:host=localhost;dbname=compreoseu;charset=utf8", 'root', '');
		
		// Recupere as informações das tabelas do banco de dados
		$query = "SHOW TABLES";
		$tables = $pdo->query($query)->fetchAll(PDO::FETCH_COLUMN);
		
		// Para cada tabela, gere a entidade correspondente
		foreach ($tables as $table) {
			// Recupere as informações das colunas da tabela
			$query = "SHOW COLUMNS FROM $table";
			$columns = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
			
			// Recupere as informações das chaves estrangeiras da tabela
			$query = "SELECT
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
              FROM
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE
              WHERE
                TABLE_NAME = '$table' AND
                CONSTRAINT_NAME <> 'PRIMARY'";
			$foreignKeys = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
			
			// Recupere as informações das tabelas de relacionamento muitos para muitos
			$query = "SELECT
                TABLE_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
              FROM
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE
              WHERE
                REFERENCED_TABLE_NAME = '$table'";
			$manyToManyTables = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
			
			// Recupere as informações das chaves primárias da tabela
		    $query = "SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'";
		    $primaryKeys = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
			
			// Gere o código da entidade
			$entityCode = "<?php\n\n";
			$entityCode .= "namespace App\\Models\\Entities; \n\n";
			$entityCode .= "use Doctrine\\ORM\\Mapping as ORM;\n\n";
			$entityCode .= "#[ORM\\Entity]\n";
			$entityCode .= "#[ORM\\Table(name: \"$table\")]\n";
			$entityCode .= "class " . ucfirst($table) . "\n";
			$entityCode .= "{\n";
			foreach ($columns as $column) {
				$columnName = $column['Field'];
				$columnType = $column['Type'];
				$nullable = $column['Null'] === 'YES' ? 'true' : 'false';
				
				$entityCode .= "    #[ORM\\Column(name: \"$columnName\", type: \"$columnType\", nullable: $nullable)]\n";
				$entityCode .= "    public $" . lcfirst($columnName) . ";\n\n";
			}
			
			foreach ($primaryKeys as $primaryKey) {
		        $columnName = $primaryKey['Column_name'];
		
		        $entityCode .= "    #[ORM\\Id]\n";
		        $entityCode .= "    #[ORM\\Column(name: \"$columnName\", type: \"integer\")]\n";
		        $entityCode .= "    #[ORM\\GeneratedValue(strategy: \"AUTO\")]\n";
		        $entityCode .= "    public $" . lcfirst($columnName) . ";\n\n";
		    }
			
			foreach ($foreignKeys as $foreignKey) {
				$columnName = $foreignKey['COLUMN_NAME'];
				$referencedTable = $foreignKey['REFERENCED_TABLE_NAME'];
				$referencedColumn = $foreignKey['REFERENCED_COLUMN_NAME'];
				
				$entityCode .= "    #[ORM\\ManyToOne(targetEntity: \"" . ucfirst($referencedTable) . "\")]\n";
				$entityCode .= "    #[ORM\\JoinColumn(name: \"$columnName\", referencedColumnName: \"$referencedColumn\")]\n";
				$entityCode .= "    public $" . lcfirst($referencedTable) . ";\n\n";
			}
			
			foreach ($manyToManyTables as $manyToManyTable) {
				$tableName = $manyToManyTable['TABLE_NAME'];
				$columnName = $manyToManyTable['COLUMN_NAME'];
				$referencedTable = $manyToManyTable['REFERENCED_TABLE_NAME'];
				$referencedColumn = $manyToManyTable['REFERENCED_COLUMN_NAME'];
				
				$entityCode .= "    #[ORM\\ManyToMany(targetEntity: \"" . ucfirst($referencedTable) . "\")]\n";
				$entityCode .= "    #[ORM\\JoinTable(\n";
				$entityCode .= "        name: \"$tableName\",\n";
				$entityCode .= "        joinColumns: [\n";
				$entityCode .= "            #[ORM\\JoinColumn(name: \"$columnName\", referencedColumnName: \"$referencedColumn\")]\n";
				$entityCode .= "        ],\n";
				$entityCode .= "        inverseJoinColumns: [\n";
				$entityCode .= "            #[ORM\\JoinColumn(name: \"$referencedColumn\", referencedColumnName: \"$columnName\")]\n";
				$entityCode .= "        ]\n";
				$entityCode .= "    )]\n";
				$entityCode .= "    public $" . lcfirst($referencedTable) . "s;\n\n";
			}
			$entityCode .= "}\n";
			
			// Salve o código gerado em um arquivo
			$fileName = __DIR__ . '/../../classes-geradas/' .ucfirst($table) . ".php";
			file_put_contents($fileName, $entityCode);
			echo "Entidade $table gerada com sucesso!\n";
		}
	}
	
	public function converterParaYml() {
		$doctrine = new Doctrine();
		
		// replace with file to your own project bootstrap
		
		// replace with mechanism to retrieve EntityManager in your app
		$entityManager = $doctrine->em;
		$entityManager->getConfiguration()->setMetadataDriverImpl(
		    new \Doctrine\ORM\Mapping\Driver\DatabaseDriver(
		        $entityManager->getConnection()->getSchemaManager()
		    )
		);
		
		$cmf = new \Doctrine\ORM\Tools\DisconnectedClassMetadataFactory();
		$cmf->setEntityManager($entityManager);
		$metadata = $cmf->getAllMetadata();
		
		$cme = new \Doctrine\ORM\Tools\Export\ClassMetadataExporter();
		$exporter = $cme->getExporter('yml', __DIR__ . '/teste');
		$exporter->setMetadata($metadata);
		$exporter->export();
	}

}