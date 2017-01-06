<?php

namespace Andvab\GeonamesBundle\Manager;

use Andvab\GeonamesBundle\Entity\AlternateNames;
use Doctrine\ORM\EntityManager;

/**
 * Class AlternateNamesManager
 * @package Andvab\GeonamesBundle\Manager
 */
class AlternateNamesManager
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
     * AlternateNamesManager constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em         = $em;
        $this->repository = $em->getRepository('AndvabGeonamesBundle:AlternateNames');
        $this->connection = $em->getConnection();

        $this->connection->getConfiguration()->setSQLLogger(null);
    }

    /**
     * @param array $data
     * @return AlternateNames
     */
    public function prepareObject($data)
    {
        $alternateName = new AlternateNames();
        $alternateName->setAlternateNameId(array_key_exists(0, $data) ? $data[0] : null);
        $alternateName->setGeonameId(array_key_exists(1, $data) ? $data[1] : null);
        $alternateName->setIsoLanguage(array_key_exists(2, $data) ? $data[2] : null);
        $alternateName->setAlternateName(array_key_exists(3, $data) ? $data[3] : null);
        $alternateName->setIsPreferredName(array_key_exists(4, $data) ? $data[4] : null);
        $alternateName->setIsShortName(array_key_exists(5, $data) ? $data[5] : null);
        $alternateName->setIsColloquial(array_key_exists(6, $data) ? $data[6] : null);
        $alternateName->setIsHistoric(array_key_exists(7, $data) ? $data[7] : null);

        return $alternateName;
    }

    /**
     * @param array $languages
     */
    public function deleteByLanguages($languages)
    {
        $this->repository->createQueryBuilder('an')
            ->delete()
            ->where('an.isoLanguage IN (:languages)')
            ->setParameter('languages', $languages)
            ->getQuery()->execute();
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function truncate()
    {
        $this->connection->query('SET FOREIGN_KEY_CHECKS=0');

        $this->connection->executeUpdate($this->connection->getDatabasePlatform()->getTruncateTableSql($this->em->getClassMetadata('AndvabGeonamesBundle:AlternateNames')->getTableName()));

        $this->connection->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
