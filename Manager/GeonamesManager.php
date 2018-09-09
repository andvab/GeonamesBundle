<?php

namespace Andvab\GeonamesBundle\Manager;

use Andvab\GeonamesBundle\Entity\Geonames;
use Doctrine\ORM\EntityManager;

/**
 * Class GeonamesManager
 * @package Andvab\GeonamesBundle\Manager
 */
class GeonamesManager
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
     * GeonamesManager constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em         = $em;
        $this->repository = $em->getRepository('AndvabGeonamesBundle:Geonames');
        $this->connection = $em->getConnection();

        $this->connection->getConfiguration()->setSQLLogger(null);
    }

    /**
     * @param array $data
     * @return Geonames
     */
    public function prepareObject($data)
    {
        $geoname = new Geonames();

        $geoname->setGeonameId(array_key_exists(0, $data) ? $data[0] : null);
        $geoname->setName(array_key_exists(1, $data) ? $data[1] : null);
        $geoname->setAsciiName(array_key_exists(2, $data) ? $data[2] : null);
        $geoname->setLatitude(array_key_exists(4, $data) ? $data[4] : null);
        $geoname->setLongitude(array_key_exists(5, $data) ? $data[5] : null);
        $geoname->setFeatureClass(array_key_exists(6, $data) ? $data[6] : null);
        $geoname->setFeatureCode(array_key_exists(7, $data) ? $data[7] : null);
        $geoname->setCountryCode(array_key_exists(8, $data) ? $data[8] : null);
        $geoname->setCc2(array_key_exists(9, $data) ? $data[9] : null);
        $geoname->setAdmin1Code(array_key_exists(10, $data) ? $data[10] : null);
        $geoname->setAdmin2Code(array_key_exists(11, $data) ? $data[11] : null);
        $geoname->setAdmin3Code(array_key_exists(12, $data) ? $data[12] : null);
        $geoname->setAdmin4Code(array_key_exists(13, $data) ? $data[13] : null);
        $geoname->setPopulation(array_key_exists(14, $data) ? $data[14] : 0);
        $geoname->setElevation(array_key_exists(15, $data) ? $data[15] : 0);
        $geoname->setDem(array_key_exists(16, $data) ? $data[16] : 0);
        $geoname->setTimezone(array_key_exists(17, $data) ? $data[17] : null);
        $geoname->setModificationDate(array_key_exists(18, $data) ? $data[18] : null);

        return $geoname;
    }

    /**
     * @param string $countryCode
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteByCountry($countryCode)
    {
        $this->connection->query('SET FOREIGN_KEY_CHECKS=0');

        $this->repository->createQueryBuilder('g')
            ->delete()
            ->where('g.countryCode = :countryCode')
            ->setParameter('countryCode', $countryCode)
            ->getQuery()->execute();

        $this->connection->query('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function truncate()
    {
        $this->connection->query('SET FOREIGN_KEY_CHECKS=0');

        $this->connection->executeUpdate($this->connection->getDatabasePlatform()->getTruncateTableSql($this->em->getClassMetadata('AndvabGeonamesBundle:Geonames')->getTableName()));

        $this->connection->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
