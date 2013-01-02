<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lar\UsuarioBundle\Entity\Usuario;

/**
 * Caja
 *
 * @ORM\Table(name="sca_caja")
 * @ORM\Entity(repositoryClass="Caja\SistemaCajaBundle\Entity\CajaRepository")
 */
class Caja
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
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=64)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=20)
     */
    private $ip;

    /**
    * @ORM\ManyToOne(targetEntity="Lar\UsuarioBundle\Entity\Usuario" )
    * @ORM\JoinColumn(name="cajero_id", referencedColumnName="id")
    **/
    protected  $cajero;

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
     * Set nombre
     *
     * @param string $nombre
     * @return Caja
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Caja
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


    /**
     * Set cajero
     *
     * @param \Lar\UsuarioBundle\Entity\Usuario $cajero
     * @return Caja
     */
    public function setCajero(\Lar\UsuarioBundle\Entity\Usuario $cajero = null)
    {
        $this->cajero = $cajero;
    
        return $this;
    }

    /**
     * Get cajero
     *
     * @return \Lar\UsuarioBundle\Entity\Usuario 
     */
    public function getCajero()
    {
        return $this->cajero;
    }
}