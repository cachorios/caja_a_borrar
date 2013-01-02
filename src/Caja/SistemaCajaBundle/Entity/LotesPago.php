<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LotesPago
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Caja\SistemaCajaBundle\Entity\LotesPagoRepository")
 */
class LotesPago
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
     * @var integer
     *
     * @ORM\Column(name="tipo_pago", type="integer")
     */
    private $tipo_pago;

    /**
     * @var float
     *
     * @ORM\Column(name="importe", type="decimal")
     */
    private $importe;


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
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return LotesPago
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
}
