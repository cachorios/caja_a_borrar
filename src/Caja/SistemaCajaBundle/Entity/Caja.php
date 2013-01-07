<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Lar\UsuarioBundle\Entity\Usuario;

/**
 * Caja
 *
 * @ORM\Table(name="sca_caja")
 * @ORM\Entity(repositoryClass="Caja\SistemaCajaBundle\Entity\CajaRepository")
 *
 * @DoctrineAssert\UniqueEntity("nombre")
 * @DoctrineAssert\UniqueEntity("cajero")
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
     * @ORM\OneToOne(targetEntity="\Lar\UsuarioBundle\Entity\Usuario")
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
     * Set cajero
     *
     * @param \Lar\UsuarioBundle\Entity\Grupo $cajero
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