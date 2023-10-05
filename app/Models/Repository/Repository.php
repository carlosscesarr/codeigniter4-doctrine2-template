<?php


namespace App\Models\Repository;

use CodeIgniter\Entity\Entity;
use Doctrine;


abstract class Repository  {

    protected static $entityName;

    protected static $em;

    static function save(Entity $entity){
        self::$em = Doctrine::retrieveEntityManager();
        self::$em->persist($entity);
        self::$em->flush();
    }

    protected static abstract function findAll();
}