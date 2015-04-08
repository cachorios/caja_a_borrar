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
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var float
     *
     * @ORM\Column(name="importe_inicial", type="decimal", nullable=false)
     */
    private $importe_inicial;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_cierre", type="datetime", nullable=true)
     */
    private $fecha_cierre;

    /**
     * @var string
     * @ORM\Column(name="direccion_ip", type="string", length=15, nullable=true)
     */
    private $direccion_ip;

    /**
     * @var string
     * @ORM\Column(name="host", type="string", length=32, nullable=true)
     */
    private $host;

    /**
     * @var string
     * @ORM\Column(name="archivo_cierre", type="string", length=32, nullable=true)
     */
    private $archivo_cierre;

    /**
     * @ORM\OneToMany(targetEntity="Lote", mappedBy="apertura")
     */
    protected $lotes;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Caja\SistemaCajaBundle\Entity\Habilitacion")
     */
    private $habilitacion;

    /**
     * @var integer
     *
     * @ORM\Column(name="proceso_incorporacion", type="decimal", nullable=true)
     */
    private $proceso_incorporacion;

    /**
     * @var integer
     *
     * @ORM\Column(name="proceso_fecha", type="datetime", nullable=true)
     */
    private $proceso_fecha;

    public function __construct()
    {
        $this->fecha = new \DateTime();
        $this->fecha_cierre = null;
        $this->importe_inicial = 0;
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

    /**
     * Add lotes
     *
     * @param \Caja\SistemaCajaBundle\Entity\Lote $lotes
     * @return Apertura
     */
    public function addLote(\Caja\SistemaCajaBundle\Entity\Lote $lotes)
    {
        $this->lotes[] = $lotes;

        return $this;
    }

    /**
     * Remove lotes
     *
     * @param \Caja\SistemaCajaBundle\Entity\Lote $lotes
     */
    public function removeLote(\Caja\SistemaCajaBundle\Entity\Lote $lotes)
    {
        $this->lotes->removeElement($lotes);
    }

    /**
     * Get lotes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLotes()
    {
        return $this->lotes;
    }

    public function getComprobanteCantidad()
    {
        $n = 0;
        foreach ($this->getLotes() as $lote) {
            foreach ($lote->getDetalle() as $detalle) {
                if (!$detalle->getAnulado()) {
                    $n++;
                }
            }
        }

        return $n;
    }

    public function getComprobanteAnulado()
    {

        $n = 0;
        foreach ($this->getLotes() as $lote) {
            foreach ($lote->getDetalle() as $detalle) {
                if ($detalle->getAnulado()) {
                    $n++;
                }
            }
        }
        return $n;
    }

    public function getImporteCobro()
    {
        $n = 0;
        try {
            foreach ($this->getLotes() as $lote) {
                $pagos = $lote->getPagos();
                foreach ($pagos as $pago) {
                    //if(!$pago->getAnulado()){
                    $n += $pago->getImporte();
                    //}
                }
            }
        } catch (\Symfony\Component\Config\Definition\Exception\Exception $e) {

        }
        return ($n == null ? 0 : $n);
    }

    public function getImporteAnulado()
    {
        $n = 0;
        try {
            foreach ($this->getLotes() as $lote) {
                $pagos = $lote->getPagos();
                foreach ($pagos as $pago) {
                    if ($pago->getImporte() < 0) {
                        $n += $pago->getImporte();
                    }
                }
            }
        } catch (\Symfony\Component\Config\Definition\Exception\Exception $e) {

        }
        return ($n == null ? 0 : -$n);
    }


    /**
     * Set direccion_ip
     *
     * @param string $direccionIp
     * @return Apertura
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
     * @return Apertura
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
     * Set archivo_cierre
     *
     * @param string $archivoCierre
     * @return Apertura
     */
    public function setArchivoCierre($archivoCierre)
    {
        $this->archivo_cierre = $archivoCierre;

        return $this;
    }

    /**
     * Get archivo_cierre
     *
     * @return string
     */
    public function getArchivoCierre()
    {
        return $this->archivo_cierre;
    }

    /**
     * Set habilitacion
     *
     * @param \Caja\SistemaCajaBundle\Entity\Habilitacion $habilitacion
     * @return Apertura
     */
    public function setHabilitacion(\Caja\SistemaCajaBundle\Entity\Habilitacion $habilitacion = null)
    {
        $this->habilitacion = $habilitacion;

        return $this;
    }

    /**
     * Get habilitacion
     *
     * @return \Caja\SistemaCajaBundle\Entity\Habilitacion
     */
    public function getHabilitacion()
    {
        return $this->habilitacion;
    }

    /**
     * Set proceso_incorporacion
     *
     * @param string $procesoIncorporacion
     * @return Apertura
     */
    public function setProcesoIncorporacion($procesoIncorporacion)
    {
        $this->proceso_incorporacion = $procesoIncorporacion;
    
        return $this;
    }

    /**
     * Get proceso_incorporacion
     *
     * @return string 
     */
    public function getProcesoIncorporacion()
    {
        return $this->proceso_incorporacion;
    }

    /**
     * Set proceso_fecha
     *
     * @param \DateTime $procesoFecha
     * @return Apertura
     */
    public function setProcesoFecha($procesoFecha)
    {
        $this->proceso_fecha = $procesoFecha;
    
        return $this;
    }

    /**
     * Get proceso_fecha
     *
     * @return \DateTime 
     */
    public function getProcesoFecha()
    {
        return $this->proceso_fecha;
    }
}