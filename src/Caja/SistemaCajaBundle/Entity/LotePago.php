<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LotePago
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Caja\SistemaCajaBundle\Entity\LotePagoRepository")
 */
class LotePago
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
     * @ORM\Column(name="anulado", nullable=true)
     */
    private $anulado;


    /**
     * @ORM\ManyToOne(targetEntity="Lote")
     */
    private $lote;

    /**
     * @ORM\ManyToOne(targetEntity="TipoPago", inversedBy="pagos")
     */
    private $tipo_pago;

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return LotesPago
     */

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

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
     * Set tipo_pago
     *
     * @param integer $tipoPago
     * @return LotesPago
     */
    public function setTipoPago($tipoPago)
    {
        $this->tipo_pago = $tipoPago;
    
        return $this;
    }

    /**
     * Get tipo_pago
     *
     * @return integer 
     */
    public function getTipoPago()
    {
        return $this->tipo_pago;
    }

    /**
     * Set importe
     *
     * @param float $importe
     * @return LotesPago
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
     * @param string $anulado
     * @return LotePago
     */
    public function setAnulado($anulado)
    {
        $this->anulado = $anulado;
    
        return $this;
    }

    /**
     * Get anulado
     *
     * @return string 
     */
    public function getAnulado()
    {
        return $this->anulado;
    }

    /**
     * Set lote
     *
     * @param \Caja\SistemaCajaBundle\Entity\Lote $lote
     * @return LotePago
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