<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * LoteAnulacion
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Caja\SistemaCajaBundle\Entity\LoteAnulacionRepository")
 */
class LoteAnulacion
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
     * @ORM\OneToMany(targetEntity="LoteAnulacionDetalle", mappedBy="lote", cascade={"persist"} )
     */
    private $detalle_anulacion;


    /**
     * @ORM\OneToOne(targetEntity="\Lar\UsuarioBundle\Entity\Usuario")
     *
     * ORM\Column(name="usuario", type="integer", nullable=true)
     **/
    protected  $usuario;


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
     * @return LoteDetalle
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
     * Constructor
     */
    public function __construct()
    {
        $this->detalle_anulacion = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add detalle_anulacion
     *
     * @param \Caja\SistemaCajaBundle\Entity\LoteAnulacionDetalle $detalleAnulacion
     * @return LoteAnulacion
     */
    public function addDetalleAnulacion(\Caja\SistemaCajaBundle\Entity\LoteAnulacionDetalle $detalleAnulacion)
    {
        $this->detalle_anulacion[] = $detalleAnulacion;

        return $this;
    }

    /**
     * Remove detalle_anulacion
     *
     * @param \Caja\SistemaCajaBundle\Entity\LoteAnulacionDetalle $detalleAnulacion
     */
    public function removeDetalleAnulacion(\Caja\SistemaCajaBundle\Entity\LoteAnulacionDetalle $detalleAnulacion)
    {
        $this->detalle_anulacion->removeElement($detalleAnulacion);
    }

    /**
     * Get detalle_anulacion
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDetalleAnulacion()
    {
        return $this->detalle_anulacion;
    }

    /**
     * Set usuario
     *
     * @param \Lar\UsuarioBundle\Entity\Usuario $usuario
     * @return LoteAnulacion
     */
    public function setUsuario(\Lar\UsuarioBundle\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \Lar\UsuarioBundle\Entity\Usuario 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }
}
