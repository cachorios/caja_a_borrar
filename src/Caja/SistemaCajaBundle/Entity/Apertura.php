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
     * @ORM\Column(name="fecha_cierre", type="date", nullable=true)
     */
    private $fecha_cierre;

    /**
     * @ORM\ManyToOne(targetEntity="Caja\SistemaCajaBundle\Entity\Caja")
     * @ORM\JoinColumn(name="caja_id", referencedColumnName="id")
     */
    protected $caja;

    /**
     * @ORM\OneToMany(targetEntity="Lote", mappedBy="apertura")
     */
    protected $lotes;

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
     * Set caja
     *
     * @param \Caja\SistemaCajaBundle\Entity\Caja $caja
     * @return Apertura
     */
    public function setCaja(\Caja\SistemaCajaBundle\Entity\Caja $caja = null)
    {
        $this->caja = $caja;

        return $this;
    }

    /**
     * Get caja
     *
     * @return \Caja\SistemaCajaBundle\Entity\Caja
     */
    public function getCaja()
    {
        return $this->caja;
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
            $n += $lote->getDetalle()->count();
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

                /*
                foreach ($pagos as $pago) {

                    if(!$pago->getAnulado()){
                        $n += $pago->getImporte() ;
                    }
                }
                  */

            }
        } catch (\Symfony\Component\Config\Definition\Exception\Exception $e) {

        }
        return ($n == null ? 0 : $n);
    }

    public function getImporteAnulado()
    {
        $n=0;
        foreach ($this->getLotes() as $lote) {
           $n += $lote->getImportePagosAnulado();
        }
        return ($n == null ? 0 : $n);
    }


}