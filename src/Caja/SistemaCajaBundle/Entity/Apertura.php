<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Apertura
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Caja\SistemaCajaBundle\Entity\AperturaRepository")
 */
class Apertura
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
     * @ORM\Column(name="fecha", type="date")
     */
    private $fecha;

    /**
     * @var float
     *
     * @ORM\Column(name="importe_inicial", type="decimal")
     */
    private $importe_inicial;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_cierre", type="date")
     */
    private $fecha_cierre;


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
     * @return Apertura
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
     * Set importe_inicial
     *
     * @param float $importeInicial
     * @return Apertura
     */
    public function setImporteInicial($importeInicial)
    {
        $this->importe_inicial = $importeInicial;
    
        return $this;
    }

    /**
     * Get importe_inicial
     *
     * @return float 
     */
    public function getImporteInicial()
    {
        return $this->importe_inicial;
    }

    /**
     * Set fecha_cierre
     *
     * @param \DateTime $fechaCierre
     * @return Apertura
     */
    public function setFechaCierre($fechaCierre)
    {
        $this->fecha_cierre = $fechaCierre;
    
        return $this;
    }

    /**
     * Get fecha_cierre
     *
     * @return \DateTime 
     */
    public function getFechaCierre()
    {
        return $this->fecha_cierre;
    }
}
