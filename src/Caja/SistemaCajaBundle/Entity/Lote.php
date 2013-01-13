<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lote
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Caja\SistemaCajaBundle\Entity\LoteRepository")
 */
class Lote
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
     * @ORM\ManyToOne(targetEntity="Apertura", inversedBy="lotes")
     */
    private $apertura;

    /**
     * @ORM\OneToMany(targetEntity="LoteDetalle", mappedBy="lote")
     */
    private $detalle;

    /**
     * @ORM\OneToMany(targetEntity="LotePago", mappedBy="lote")
     */
    private $pagos;


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
     * @return Lote
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
     * Set apertura
     *
     * @param \Caja\SistemaCajaBundle\Entity\Apertura $apertura
     * @return Lote
     */
    public function setApertura(\Caja\SistemaCajaBundle\Entity\Apertura $apertura = null)
    {
        $this->apertura = $apertura;
    
        return $this;
    }

    /**
     * Get apertura
     *
     * @return \Caja\SistemaCajaBundle\Entity\Apertura 
     */
    public function getApertura()
    {
        return $this->apertura;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->detalle = new \Doctrine\Common\Collections\ArrayCollection();
        $this->pagos = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add detalle
     *
     * @param \Caja\SistemaCajaBundle\Entity\LoteDetalle $detalle
     * @return Lote
     */
    public function addDetalle(\Caja\SistemaCajaBundle\Entity\LoteDetalle $detalle)
    {
        $this->detalle[] = $detalle;
    
        return $this;
    }

    /**
     * Remove detalle
     *
     * @param \Caja\SistemaCajaBundle\Entity\LoteDetalle $detalle
     */
    public function removeDetalle(\Caja\SistemaCajaBundle\Entity\LoteDetalle $detalle)
    {
        $this->detalle->removeElement($detalle);
    }

    /**
     * Get detalle
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDetalle()
    {
        return $this->detalle;
    }

    /**
     * Add pagos
     *
     * @param \Caja\SistemaCajaBundle\Entity\LotePago $pagos
     * @return Lote
     */
    public function addPago(\Caja\SistemaCajaBundle\Entity\LotePago $pagos)
    {
        $this->pagos[] = $pagos;
    
        return $this;
    }

    /**
     * Remove pagos
     *
     * @param \Caja\SistemaCajaBundle\Entity\LotePago $pagos
     */
    public function removePago(\Caja\SistemaCajaBundle\Entity\LotePago $pagos)
    {
        $this->pagos->removeElement($pagos);
    }

    /**
     * Get pagos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPagos()
    {
        return $this->pagos;
    }
}