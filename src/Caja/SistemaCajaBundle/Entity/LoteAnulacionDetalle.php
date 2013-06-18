<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * LoteDetalle
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Caja\SistemaCajaBundle\Entity\LoteDetalleRepository")
 */
class LoteAnulacionDetalle
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
     * @ORM\ManyToOne(targetEntity="LoteAnulacion", inversedBy="detalle_anulucion")
     */
    private $lote;


    /**
     * @ORM\ManyToOne(targetEntity="LoteDetalle")
     */
    private $detalle;


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
     * Set lote
     *
     * @param \Caja\SistemaCajaBundle\Entity\LoteAnulacion $lote
     * @return LoteDetalle
     */
    public function setLote(\Caja\SistemaCajaBundle\Entity\LoteAnulacion $lote = null)
    {
        $this->lote = $lote;
    
        return $this;
    }

    /**
     * Get lote
     *
     * @return \Caja\SistemaCajaBundle\Entity\LoteAnulacion
     */
    public function getLote()
    {
        return $this->lote;
    }


    /**
     * Set detalle
     *
     * @param \Caja\SistemaCajaBundle\Entity\LoteAnulacion $lote
     * @return LoteDetalle
     */
    public function setDetalle(\Caja\SistemaCajaBundle\Entity\LoteDetalle $detalle = null)
    {
        $this->detalle = $detalle;

        return $this;
    }

    /**
     * Get loteDetalle
     *
     * @return \Caja\SistemaCajaBundle\Entity\LoteDetalle
     */
    public function getDetalle()
    {
        return $this->detalle;
    }







    public function __toString()
    {
        return "Lote ".$this->getId();
    }



}