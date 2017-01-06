<?php

namespace Andvab\GeonamesBundle\Manager;

use Andvab\GeonamesBundle\Entity\Countries;
use Doctrine\ORM\EntityManager;

/**
 * Class CountriesManager
 * @package Andvab\GeonamesBundle\Manager
 */
class CountriesManager
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
     * CountriesManager constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em         = $em;
        $this->repository = $em->getRepository('AndvabGeonamesBundle:Countries');
        $this->connection = $em->getConnection();

        $this->connection->getConfiguration()->setSQLLogger(null);
    }

    /**
     * @param array $data
     */
    public function create($data)
    {
        $country = new Countries();

        $country->setIso(array_key_exists(0, $data) ? $data[0] : null);
        $country->setIso3(array_key_exists(1, $data) ? $data[1] : null);
        $country->setIsoNumeric(array_key_exists(2, $data) ? $data[2] : null);
        $country->setFips(array_key_exists(3, $data) ? $data[3] : null);
        $country->setCountry(array_key_exists(4, $data) ? $data[4] : null);
        $country->setCapital(array_key_exists(5, $data) ? $data[5] : null);
        $country->setArea(array_key_exists(6, $data) ? $data[6] : null);
        $country->setPopulation(array_key_exists(7, $data) ? $data[7] : null);
        $country->setContinent(array_key_exists(8, $data) ? $data[8] : null);
        $country->setTld(array_key_exists(9, $data) ? $data[9] : null);
        $country->setCurrencyCode(array_key_exists(10, $data) ? $data[10] : null);
        $country->setCurrencyName(array_key_exists(11, $data) ? $data[11] : null);
        $country->setPhone(array_key_exists(12, $data) ? $data[12] : null);
        $country->setPostalCodeFormat(array_key_exists(13, $data) ? $data[13] : null);
        $country->setPostalCodeRegex(array_key_exists(14, $data) ? $data[14] : null);
        $country->setLanguages(array_key_exists(15, $data) ? $data[15] : null);
        $country->setGeonameid(array_key_exists(16, $data) ? $data[16] : null);
        $country->setNeighbours(array_key_exists(17, $data) ? $data[17] : null);
        $country->setEquivalentFipsCode(array_key_exists(18, $data) ? $data[18] : null);

        $this->em->persist($country);
        $this->em->flush();
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function truncate()
    {
        $this->connection->query('SET FOREIGN_KEY_CHECKS=0');

        $this->connection->executeUpdate($this->connection->getDatabasePlatform()->getTruncateTableSql($this->em->getClassMetadata('AndvabGeonamesBundle:Countries')->getTableName()));

        $this->connection->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
