<?php
namespace Lar\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LogIngreso
 *
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass="Lar\UsuarioBundle\Entity\LogIngresoRepository")
 */


class LogIngreso
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Lar\UsuarioBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=256)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=16)
     */
    private $ip;

    /**
     * @var datetime
     *
     * @ORM\Column(name="fecha", type="datetime")
     *
     */
    private $fecha;


    public function __construct($usuario = null, $ip, $msg)
    {
        $this->setUsuario($usuario);
        $this->setDescripcion($msg);
        $this->setIp($ip);
        $this->setFecha(new \DateTime('now'));
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return LogIngreso
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set usuario
     *
     * @param \Lar\UsuarioBundle\Entity\Usuario $usuario
     * @return LogIngreso
     */
    public function setUsuario(\Lar\UsuarioBundle\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \Lar\UsuarioBundle\Entity\Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return LogIngreso
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return LogIngreso
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    
        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }
}