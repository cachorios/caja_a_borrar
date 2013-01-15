<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VtoImporteCodigoBarra
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class VtoImporteCodigoBarra
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
     * @ORM\Column(name="vencimiento", type="string", length=64)
     */
    private $vencimiento;

    /**
     * @var string
     *
     * @ORM\Column(name="importe", type="string", length=64)
     */
    private $importe;


    /**
     * @ORM\ManyToOne(targetEntity="CodigoBarra", inversedBy="vtos_importes")
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
     * Set vencimiento
     *
     * @param string $vencimiento
     * @return VtoImporteCodigoBarra
     */
    public function setVencimiento($vencimiento)
    {
        $this->vencimiento = $vencimiento;
    
        return $this;
    }

    /**
     * Get vencimiento
     *
     * @return string 
     */
    public function getVencimiento()
    {
        return $this->vencimiento;
    }

    /**
     * Set importe
     *
     * @param string $importe
     * @return VtoImporteCodigoBarra
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;
    
        return $this;
    }

    /**
     * Get importe
     *
     * @return string 
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * Set codigobarra
     *
     * @param \Caja\SistemaCajaBundle\Entity\CodigoBarra $codigobarra
     * @return VtoImporteCodigoBarra
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