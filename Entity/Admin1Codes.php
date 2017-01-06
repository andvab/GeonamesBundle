<?php

namespace Andvab\GeonamesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Admin1Codes
 * @ORM\Entity
 * @ORM\Table(name="andvab_geonames_admin1_codes", indexes={@ORM\Index(name="geoname_id", columns={"geoname_id"}), @ORM\Index(name="code", columns={"code"})})
 */
class Admin1Codes
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="code", type="string", length=100)
     */
    protected $code;

    /**
     * @ORM\Column(name="name", type="string", length=200, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(name="ascii_name", type="string", length=200, nullable=true)
     */
    protected $asciiName;

    /**
     * @ORM\Column(name="geoname_id", type="integer", nullable=true)
     */
    protected $geonameId;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Admin1Codes
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Admin1Codes
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set asciiName
     *
     * @param string $asciiName
     * @return Admin1Codes
     */
    public function setAsciiName($asciiName)
    {
        $this->asciiName = $asciiName;

        return $this;
    }

    /**
     * Get asciiName
     *
     * @return string
     */
    public function getAsciiName()
    {
        return $this->asciiName;
    }

    /**
     * Set geonameId
     *
     * @param integer $geonameId
     * @return Admin1Codes
     */
    public function setGeonameId($geonameId)
    {
        $this->geonameId = $geonameId;

        return $this;
    }

    /**
     * Get geonameId
     *
     * @return integer
     */
    public function getGeonameId()
    {
        return $this->geonameId;
    }
}
