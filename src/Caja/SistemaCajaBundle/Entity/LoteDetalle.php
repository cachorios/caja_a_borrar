<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LoteDetalle
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Caja\SistemaCajaBundle\Entity\LoteDetalleRepository")
 */
class LoteDetalle
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
     * @ORM\Column(name="codigo_barra", type="string", length=255)
     */
    private $codigo_barra;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var float
     *
     * @ORM\Column(name="importe", type="decimal")
     */
    private $importe;

    /**
     * @var boolean
     *
     * @ORM\Column(name="anulado", type="boolean")
     */
    private $anulado;

    /**
     * @ORM\ManyToOne(targetEntity="Lote")
     */
    private $lote;


    public function __construct(){
        $this->anulado = 0;
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
     * Set codigo_barra
     *
     * @param string $codigoBarra
     * @return LoteDetalle
     */
    public function setCodigoBarra($codigoBarra)
    {
        $this->codigo_barra = $codigoBarra;
    
        return $this;
    }

    /**
     * Get codigo_barra
     *
     * @return string 
     */
    public function getCodigoBarra()
    {
        return $this->codigo_barra;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return LoteDetalle
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
     * Set importe
     *
     * @param float $importe
     * @return LoteDetalle
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;
    
        return $this;
    }

    /**
     * Get importe
     *
     * @return float 
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * Set anulado
     *
     * @param boolean $anulado
     * @return LoteDetalle
     */
    public function setAnulado($anulado)
    {
        $this->anulado = $anulado;
    
        return $this;
    }

    /**
     * Get anulado
     *
     * @return boolean 
     */
    public function getAnulado()
    {
        return $this->anulado;
    }

    /**
     * Set lote
     *
     * @param \Caja\SistemaCajaBundle\Entity\Lote $lote
     * @return LoteDetalle
     */
    public function setLote(\Caja\SistemaCajaBundle\Entity\Lote $lote = null)
    {
        $this->lote = $lote;
    
        return $this;
    }

    /**
     * Get lote
     *
     * @return \Caja\SistemaCajaBundle\Entity\Lote 
     */
    public function getLote()
    {
        return $this->lote;
    }
}