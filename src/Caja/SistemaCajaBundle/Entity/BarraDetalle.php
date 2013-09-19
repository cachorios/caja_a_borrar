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
     * El estado indica si es visible = 1 o si es Comprobante = 2
     * @var smallint
     * @ORM\Column(name="estado", type="smallint", nullable=true)
     */
    private $estado;

    private $ver;
    private $seccion;
    private $comp;

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
     * Set estado
     *
     * @param integer $estado
     * @return BarraDetalle
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
        $this->calcEstado($estado);
        return $this;
    }



    /**
     * Get estado
     *
     * @return integer
     */
    public function getEstado()
    {
        return $this->estado  ;
    }

    public function getVer()
    {
        if($this->ver == null ){
            $this->calcEstado($this->getEstado());
        }
        return  $this->ver == 1 ;
    }

    public function getSeccion()
    {
        if($this->seccion == null ){
            $this->calcEstado($this->getEstado());
        }
        return $this->seccion == 1;
    }

    public function getComp()
    {
        if($this->comp == null ){
            $this->calcEstado($this->getEstado());
        }
        return $this->comp == 1;
    }

    public function setVer( $ver = 0)
    {
        $this->ver = $ver;
        $this->calcEstadoToSet();
        return $this;
    }

    public function setSeccion( $s = 0)
    {
        $this->seccion = $s;
        $this->calcEstadoToSet();
        return $this;
    }

    public function setComp($comp = 0)
    {
        $this->comp = $comp;
        $this->calcEstadoToSet();
        return $this;
    }

    private function calcEstado($estado = 0){

        $this->comp = intval($estado / 100);
        $this->seccion =  intval(($estado - $this->comp *100) /10);
        $this->ver = $estado - $this->comp *100 - $this->seccion *10;

    }

    private function calcEstadoToSet(){

        $estado =   ($this->ver == null ? 0 :$this->ver)+
                    ($this->seccion == null ? 0 :$this->seccion * 10)+
                    ($this->comp == null ? 0 :$this->comp * 100);
        $this->estado = $estado;


    }

}