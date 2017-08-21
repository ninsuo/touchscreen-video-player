<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ban
 *
 * @ORM\Table(name="ban")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BanRepository")
 */
class Ban
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="ip", type="integer", unique=true)
     */
    private $ip;

    /**
     * @var int
     *
     * @ORM\Column(name="tries", type="integer")
     */
    private $tries;

    /**
     * @var int
     *
     * @ORM\Column(name="timestamp", type="integer")
     */
    private $timestamp;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ip
     *
     * @param integer $ip
     *
     * @return Ban
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return int
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set tries
     *
     * @param integer $tries
     *
     * @return Ban
     */
    public function setTries($tries)
    {
        $this->tries = $tries;

        return $this;
    }

    /**
     * Get tries
     *
     * @return int
     */
    public function getTries()
    {
        return $this->tries;
    }

    /**
     * Set timestamp
     *
     * @param integer $timestamp
     *
     * @return Ban
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}

