<?php

namespace Andvab\GeonamesBundle\Manager;

use Andvab\GeonamesBundle\Entity\Admin1Codes;
use Doctrine\ORM\EntityManager;

/**
 * Class Admin1CodesManager
 * @package Andvab\GeonamesBundle\Manager
 */
class Admin1CodesManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repository;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * Admin1CodesManager constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em         = $em;
        $this->repository = $em->getRepository('AndvabGeonamesBundle:Admin1Codes');
        $this->connection = $em->getConnection();

        $this->connection->getConfiguration()->setSQLLogger(null);
    }

    /**
     * @param string  $code
     * @param string  $name
     * @param string  $asciiName
     * @param integer $geonameId
     * @return Admin1Codes
     */
    public function create($code, $name, $asciiName, $geonameId)
    {
        $admin1Code = new Admin1Codes();
        $admin1Code->setCode($code);
        $admin1Code->setName($name);
        $admin1Code->setAsciiName($asciiName);
        $admin1Code->setGeonameId($geonameId);

        return $admin1Code;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function truncate()
    {
        $this->connection->query('SET FOREIGN_KEY_CHECKS=0');

        $this->connection->executeUpdate($this->connection->getDatabasePlatform()->getTruncateTableSql($this->em->getClassMetadata('AndvabGeonamesBundle:Admin1Codes')->getTableName()));

        $this->connection->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
