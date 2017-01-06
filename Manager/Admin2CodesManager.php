<?php

namespace Andvab\GeonamesBundle\Manager;

use Andvab\GeonamesBundle\Entity\Admin2Codes;
use Doctrine\ORM\EntityManager;

/**
 * Class Admin2CodesManager
 * @package Andvab\GeonamesBundle\Manager
 */
class Admin2CodesManager
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
     * Admin2CodesManager constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em         = $em;
        $this->repository = $em->getRepository('AndvabGeonamesBundle:Admin2Codes');
        $this->connection = $em->getConnection();

        $this->connection->getConfiguration()->setSQLLogger(null);
    }

    /**
     * @param string  $code
     * @param string  $name
     * @param string  $asciiName
     * @param integer $geonameId
     * @return Admin2Codes
     */
    public function create($code, $name, $asciiName, $geonameId)
    {
        $admin2Code = new Admin2Codes();
        $admin2Code->setCode($code);
        $admin2Code->setName($name);
        $admin2Code->setAsciiName($asciiName);
        $admin2Code->setGeonameId($geonameId);

        return $admin2Code;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function truncate()
    {
        $this->connection->query('SET FOREIGN_KEY_CHECKS=0');

        $this->connection->executeUpdate($this->connection->getDatabasePlatform()->getTruncateTableSql($this->em->getClassMetadata('AndvabGeonamesBundle:Admin2Codes')->getTableName()));

        $this->connection->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
