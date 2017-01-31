<?php

namespace DigiLoginBundle\Entity;

use BaseBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * DigiLogin
 *
 * @ORM\Table(name="digi_login")
 * @ORM\Entity(repositoryClass="DigiLoginBundle\Repository\DigiLoginRepository")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class DigiLogin
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="BaseBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=32, unique=true)
     * @Assert\Length(min=8, max=32)
     * @Assert\Regex(pattern="/^\d+$/")
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="pin", type="string", length=128, nullable=true)
     * @Assert\Length(min=4, max=16)
     * @Assert\Regex(pattern="/^\d+$/")
     */
    private $pin;

    /**
     * @var int
     *
     * @ORM\Column(name="cur_tries", type="integer")
     */
    private $curTries = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="max_tries", type="integer")
     * @Assert\Range(min=1, max=50)
     * @Assert\Regex(pattern="/^\d+$/")
     */
    private $maxTries = 10;

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
     * Set user
     *
     * @param User $user
     *
     * @return DigiLogin
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set login
     *
     * @param string $login
     *
     * @return DigiLogin
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set pin
     *
     * @param string $pin
     *
     * @return DigiLogin
     */
    public function setPin($pin)
    {
        $this->pin = $pin;

        return $this;
    }

    /**
     * Get pin
     *
     * @return string
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * Set curTries
     *
     * @param integer $curTries
     *
     * @return DigiLogin
     */
    public function setCurTries($curTries)
    {
        $this->curTries = $curTries;

        return $this;
    }

    /**
     * Get curTries
     *
     * @return int
     */
    public function getCurTries()
    {
        return $this->curTries;
    }

    /**
     * Set maxTries
     *
     * @param string $maxTries
     *
     * @return DigiLogin
     */
    public function setMaxTries($maxTries)
    {
        $this->maxTries = $maxTries;

        return $this;
    }

    /**
     * Get maxTries
     *
     * @return string
     */
    public function getMaxTries()
    {
        return $this->maxTries;
    }
}

