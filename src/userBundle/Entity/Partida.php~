<?php

namespace userBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Partida
 *
 * @ORM\Table(name="partidas")
 * @ORM\Entity(repositoryClass="userBundle\Entity\PartidaRepository")
 */
class Partida
{

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="partidas")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombrePiloto", type="string", length=150)
     */
    private $nombrePiloto;

    /**
     * @var string
     *
     * @ORM\Column(name="raza", type="string", length=100)
     */
    private $raza;

    /**
     * @var string
     *
     * @ORM\Column(name="ultimoPlaneta", type="string", length=150)
     */
    private $ultimoPlaneta;

    /**
     * @var integer
     *
     * @ORM\Column(name="disparos", type="integer")
     */
    private $disparos;

    /**
     * @var integer
     *
     * @ORM\Column(name="deads", type="integer")
     */
    private $deads;

    /**
     * @var integer
     *
     * @ORM\Column(name="score", type="integer")
     */
    private $score;

    /**
     * @var integer
     *
     * @ORM\Column(name="life", type="integer")
     */
    private $life;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

   
    public function __construct()
    {
        $this->isActive = true;
    }

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
     * Set nombrePiloto
     *
     * @param string $nombrePiloto
     *
     * @return Partida
     */
    public function setNombrePiloto($nombrePiloto)
    {
        $this->nombrePiloto = $nombrePiloto;

        return $this;
    }

    /**
     * Get nombrePiloto
     *
     * @return string
     */
    public function getNombrePiloto()
    {
        return $this->nombrePiloto;
    }

    /**
     * Set raza
     *
     * @param string $raza
     *
     * @return Partida
     */
    public function setRaza($raza)
    {
        $this->raza = $raza;

        return $this;
    }

    /**
     * Get raza
     *
     * @return string
     */
    public function getRaza()
    {
        return $this->raza;
    }

    /**
     * Set ultimoPlaneta
     *
     * @param string $ultimoPlaneta
     *
     * @return Partida
     */
    public function setUltimoPlaneta($ultimoPlaneta)
    {
        $this->ultimoPlaneta = $ultimoPlaneta;

        return $this;
    }

    /**
     * Get ultimoPlaneta
     *
     * @return string
     */
    public function getUltimoPlaneta()
    {
        return $this->ultimoPlaneta;
    }

    /**
     * Set disparos
     *
     * @param integer $disparos
     *
     * @return Partida
     */
    public function setDisparos($disparos)
    {
        $this->disparos = $disparos;

        return $this;
    }

    /**
     * Get disparos
     *
     * @return integer
     */
    public function getDisparos()
    {
        return $this->disparos;
    }

    /**
     * Set deads
     *
     * @param integer $deads
     *
     * @return Partida
     */
    public function setDeads($deads)
    {
        $this->deads = $deads;

        return $this;
    }

    /**
     * Get deads
     *
     * @return integer
     */
    public function getDeads()
    {
        return $this->deads;
    }

    /**
     * Set score
     *
     * @param integer $score
     *
     * @return Partida
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set life
     *
     * @param integer $life
     *
     * @return Partida
     */
    public function setLife($life)
    {
        $this->life = $life;

        return $this;
    }

    /**
     * Get life
     *
     * @return integer
     */
    public function getLife()
    {
        return $this->life;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Partida
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Partida
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Partida
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set user
     *
     * @param \userBundle\Entity\User $user
     *
     * @return Partida
     */
    public function setUser(\userBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \userBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    
}
