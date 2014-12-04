<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BarraSeccion
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Caja\SistemaCajaBundle\Entity\BarraSeccionRepository")
 */
class BarraSeccion
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
     * @var Caja\SistemaCajaBundle\Entity\CodigoBarra
     * @ORM\ManyToOne(targetEntity="CodigoBarra", inversedBy="secciones" )
     */
    private $codigobarra;

    /**
     * @var string
     * @ORM\Column(name="descripcion", type="string", length=64)
     */
    private $descripcion;

    /**
     * @var integer
     * @ORM\Column(name="inicio", type="integer")
     */
    private $inicio;

    /**
     * @var integer
     * @ORM\Column(name="longitud", type="integer")
     */
    private $longitud;

    /**
     * @var integer
     * @ORM\Column(name="valor", type="string", length=8)
     */
    private $valor;

    /**
     * @var integer
     * @ORM\Column(name="codigo_t10", type="integer")
     */
    private $codigo_t10;



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
     * @return BarraSeccion
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
     * Set inicio
     *
     * @param integer $inicio
     * @return BarraSeccion
     */
    public function setInicio($inicio)
    {
        $this->inicio = $inicio;
    
        return $this;
    }

    /**
     * Get inicio
     *
     * @return integer 
     */
    public function getInicio()
    {
        return $this->inicio;
    }

    /**
     * Set longitud
     *
     * @param integer $longitud
     * @return BarraSeccion
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
     * Set valor
     *
     * @param string $valor
     * @return BarraSeccion
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
    
        return $this;
    }

    /**
     * Get valor
     *
     * @return string 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set codigo_t10
     *
     * @param integer $codigoT10
     * @return BarraSeccion
     */
    public function setCodigoT10($codigoT10)
    {
        $this->codigo_t10 = $codigoT10;
    
        return $this;
    }

    /**
     * Get codigo_t10
     *
     * @return integer 
     */
    public function getCodigoT10()
    {
        return $this->codigo_t10;
    }

    /**
     * Set codigobarra
     *
     * @param \Caja\SistemaCajaBundle\Entity\CodigoBarra $codigobarra
     * @return BarraSeccion
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
}