<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Puesto
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Caja\SistemaCajaBundle\Entity\PuestoRepository")
 */
class Puesto
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
     * @var integer
     * @ORM\ManyToOne(targetEntity="Caja\SistemaCajaBundle\Entity\Delegacion")
     */
    private $delegacion;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=64)
     */
    private $descripcion;

    /**
     * @var integer
     *
     * @ORM\Column(name="nro_box", type="integer")
     */
    private $nroBox;

    /**
     * @var string
     *
     * @ORM\Column(name="puerto", type="string", length=10)
     */
    private $puerto;


   

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
     * @return Puesto
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
     * Set nroBox
     *
     * @param integer $nroBox
     * @return Puesto
     */
    public function setNroBox($nroBox)
    {
        $this->nroBox = $nroBox;
    
        return $this;
    }

    /**
     * Get nroBox
     *
     * @return integer 
     */
    public function getNroBox()
    {
        return $this->nroBox;
    }

    /**
     * Set puerto
     *
     * @param string $puerto
     * @return Puesto
     */
    public function setPuerto($puerto)
    {
        $this->puerto = $puerto;
    
        return $this;
    }

    /**
     * Get puerto
     *
     * @return string 
     */
    public function getPuerto()
    {
        return $this->puerto;
    }

    /**
     * Set delegacion
     *
     * @param \Caja\SistemaCajaBundle\Entity\Delegacion $delegacion
     * @return Puesto
     */
    public function setDelegacion(\Caja\SistemaCajaBundle\Entity\Delegacion $delegacion = null)
    {
        $this->delegacion = $delegacion;
    
        return $this;
    }

    /**
     * Get delegacion
     *
     * @return \Caja\SistemaCajaBundle\Entity\Delegacion 
     */
    public function getDelegacion()
    {
        return $this->delegacion;
    }

    public function __toString(){
        return $this->getDescripcion();
    }
}