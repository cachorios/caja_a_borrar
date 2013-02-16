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
	 * @var String
	 *
	 * @ORM\Column(name="comprobante", type="string", type= "string", length=20  )
	 */
	private $comprobante;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var float
     *
     * @ORM\Column(name="importe", type="decimal", precision = 15, scale = 2  )
     */
    private $importe;

    /**
     * @var boolean
     *
     * @ORM\Column(name="anulado", type="boolean")
     */
    private $anulado;

    /**
     * @ORM\ManyToOne(targetEntity="Lote", inversedBy="detalle")
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

    public function __toString()
    {
        return "Lote ".$this->getId();
    }

    /**
     * Set comprobante
     *
     * @param string $comprobante
     * @return LoteDetalle
     */
    public function setComprobante($comprobante)
    {
        $this->comprobante = $comprobante;
    
        return $this;
    }

    /**
     * Get comprobante
     *
     * @return string 
     */
    public function getComprobante()
    {
        return $this->comprobante;
    }
}