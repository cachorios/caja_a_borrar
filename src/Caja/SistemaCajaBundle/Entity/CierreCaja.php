<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CierreCaja
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Caja\SistemaCajaBundle\Entity\CierreCajaRepository")
 */
class CierreCaja
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
     * @ORM\Column(name="importe", type="decimal")
     */
    private $importe;

    /**
     * @var datetime
     *
     * @ORM\Column(name="fecha_cierre", type="datetime")
     *
     */
    private $fecha_cierre;

    /**
     * @var string
     * @ORM\Column(name="direccion_ip", type="string", length=15)
     */
    private $direccion_ip;

    /**
     * @var string
     * @ORM\Column(name="host", type="string", length=32)
     */
    private $host;

    /**
     * @var string
     * @ORM\Column(name="cajero", type="string", length=32)
     */
    private $cajero;

    /**
     * @var integer
     * @ORM\Column(name="nro_caja", type="integer")
     */
    private $nro_caja;


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
     * @return CierreCaja
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
     * @return CierreCaja
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
     * Set fecha_cierre
     *
     * @param \DateTime $fechaCierre
     * @return CierreCaja
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

    /**
     * Set direccion_ip
     *
     * @param string $direccionIp
     * @return CierreCaja
     */
    public function setDireccionIp($direccionIp)
    {
        $this->direccion_ip = $direccionIp;
    
        return $this;
    }

    /**
     * Get direccion_ip
     *
     * @return string 
     */
    public function getDireccionIp()
    {
        return $this->direccion_ip;
    }

    /**
     * Set host
     *
     * @param string $host
     * @return CierreCaja
     */
    public function setHost($host)
    {
        $this->host = $host;
    
        return $this;
    }

    /**
     * Get host
     *
     * @return string 
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set cajero
     *
     * @param string $cajero
     * @return CierreCaja
     */
    public function setCajero($cajero)
    {
        $this->cajero = $cajero;
    
        return $this;
    }

    /**
     * Get cajero
     *
     * @return string 
     */
    public function getCajero()
    {
        return $this->cajero;
    }

    /**
     * Set nro_caja
     *
     * @param integer $nroCaja
     * @return CierreCaja
     */
    public function setNroCaja($nroCaja)
    {
        $this->nro_caja = $nroCaja;
    
        return $this;
    }

    /**
     * Get nro_caja
     *
     * @return integer 
     */
    public function getNroCaja()
    {
        return $this->nro_caja;
    }
}