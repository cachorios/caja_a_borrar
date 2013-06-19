<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoPago
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Caja\SistemaCajaBundle\Entity\TipoPagoRepository")
 */
class TipoPago
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
     * @ORM\Column(name="descripcion", type="string", length=32)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="string", length=255, nullable=true)
     */
    private $observacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="divisible", type="boolean", nullable = true)
     */
    private $divisible;

//    /**
//     * @ORM\OneToMany(targetEntity="LotePago", mappedBy="tipo_pago" )
//     */
//    private $pagos;
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
     * @return TipoPago
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
     * Set observacion
     *
     * @param string $observacion
     * @return TipoPago
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    
        return $this;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set divisible
     *
     * @param boolean $divisible
     * @return TipoPago
     */
    public function setDivisible($divisible)
    {
        $this->divisible = $divisible;
    
        return $this;
    }

    /**
     * Get divisible
     *
     * @return boolean 
     */
    public function getDivisible()
    {
        return $this->divisible;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        //$this->pagos = new \Doctrine\Common\Collections\ArrayCollection();
		$this->divisible = false;
    }

	public function __toString()
	{
		return $this->getDescripcion();
	}
//
//    /**
//     * Add pagos
//     *
//     * @param \Caja\SistemaCajaBundle\Entity\LotePago $pagos
//     * @return TipoPago
//     */
//    public function addPago(\Caja\SistemaCajaBundle\Entity\LotePago $pagos)
//    {
//        $this->pagos[] = $pagos;
//
//        return $this;
//    }
//
//    /**
//     * Remove pagos
//     *
//     * @param \Caja\SistemaCajaBundle\Entity\LotePago $pagos
//     */
//    public function removePago(\Caja\SistemaCajaBundle\Entity\LotePago $pagos)
//    {
//        $this->pagos->removeElement($pagos);
//    }
//
//    /**
//     * Get pagos
//     *
//     * @return \Doctrine\Common\Collections\Collection
//     */
//    public function getPagos()
//    {
//        return $this->pagos;
//    }
}
