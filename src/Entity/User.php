<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="tasks_users")})
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    const DEFAULT_PASSWORD = 'aQuLiP56';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Tasks", mappedBy="user", cascade={"persist", "remove"})
     */
    private $task;

    /**
     * @ORM\Column(name="login", type="string", length=100, nullable=false, unique=true)
     */
    protected $login;

    /**
     * @ORM\Column(name="password", type="string", length=100, nullable=true)
     */
    protected $password;

    /**
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(name="admin", type="boolean", nullable=true)
     */
    protected $admin;

    /**
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->task = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $login
     * @return User
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = md5($password);

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param boolean $admin
     * @return User
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getAdmin()
    {
        return $this->admin;
    }
}
