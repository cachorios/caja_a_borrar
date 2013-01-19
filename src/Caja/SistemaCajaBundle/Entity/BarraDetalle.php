<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BarraDetalle
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Caja\SistemaCajaBundle\Entity\BarraDetalleRepository")
 */
class BarraDetalle
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
     * @ORM\Column(name="descripcion", type="string", length=64)
     */
    private $descripcion;

    /**
     * @var integer
     * @ORM\Column(name="posicion", type="integer")
     */
    private $posicion;

    /**
     * @var integer
     * @ORM\Column(name="longitud", type="integer")
     */
    private $longitud;

    /**
     * @var integer
     * @ORM\Column(name="tabla", type="integer", nullable = true)
     */
    private $tabla;

    /**
     * @var integer
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;


    /**
     * Bidireccional - Muchos comentarios fueron redactados por un usuario (Lado propietario)
     *
     * @var Caja\SistemaCajaBundle\Entity\CodigoBarra
     * @ORM\ManyToOne(targetEntity="CodigoBarra", inversedBy="posiciones" )
     */
    private $codigobarra;


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
     * @return BarraDetalle
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
     * Set posicion
     *
     * @param integer $posicion
     * @return BarraDetalle
     */
    public function setPosicion($posicion)
    {
        $this->posicion = $posicion;
    
        return $this;
    }

    /**
     * Get posicion
     *
     * @return integer 
     */
    public function getPosicion()
    {
        return $this->posicion;
    }

    /**
     * Set longitud
     *
     * @param integer $longitud
     * @return BarraDetalle
     */
    public function setLongitud($longitud)
    {
        $this->longitud = $longitud;
    
        return $this;
    }

    /**
     * Get longitud
     *
     * @return integer 
     */
    public function getLongitud()
    {
        return $this->longitud;
    }

    /**
     * Set tabla
     *
     * @param integer $tabla
     * @return BarraDetalle
     */
    public function setTabla($tabla)
    {
        $this->tabla = $tabla;
    
        return $this;
    }

    /**
     * Get tabla
     *
     * @return integer 
     */
    public function getTabla()
    {
        return $this->tabla;
    }


    /**
     * Set codigobarra
     *
     * @param \Caja\SistemaCajaBundle\Entity\CodigoBarra $codigobarra
     * @return BarraDetalle
     */
    public function setCodigobarra(\Caja\SistemaCajaBundle\Entity\CodigoBarra $codigobarra = null)
    {
        $this->codigobarra = $codigobarra;
    
        return $this;
    }

    /**
     * Get codigobarra
     *
     * @return \Caja\SistemaCajaBundle\Entity\CodigoBarra 
     */
    public function getCodigobarra()
    {
        return $this->codigobarra;
    }



    /**
     * Set visible
     *
     * @param boolean $visible
     * @return BarraDetalle
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    
        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }
}