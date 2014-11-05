<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Habilitacion
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Caja\SistemaCajaBundle\Entity\HabilitacionRepository")
 */
class Habilitacion
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
     * @ORM\ManyToOne(targetEntity="Caja\SistemaCajaBundle\Entity\Caja")
     */
    protected $caja;

    /**
     * @ORM\OneToOne(targetEntity="\Lar\UsuarioBundle\Entity\Usuario")
     **/
    protected  $usuario;

    /**
     * @ORM\OneToOne(targetEntity="\Caja\SistemaCajaBundle\Entity\Puesto")
     **/
    private $puesto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="desde", type="date")
     */
    private $desde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hasta", type="date", nullable=true)
     */
    private $hasta;


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
     * Set desde
     *
     * @param \DateTime $desde
     * @return Habilitacion
     */
    public function setDesde($desde)
    {
        $this->desde = $desde;
    
        return $this;
    }

    /**
     * Get desde
     *
     * @return \DateTime 
     */
    public function getDesde()
    {
        return $this->desde;
    }

    /**
     * Set hasta
     *
     * @param \DateTime $hasta
     * @return Habilitacion
     */
    public function setHasta($hasta)
    {
        $this->hasta = $hasta;
    
        return $this;
    }

    /**
     * Get hasta
     *
     * @return \DateTime 
     */
    public function getHasta()
    {
        return $this->hasta;
    }

    /**
     * Set caja
     *
     * @param \Caja\SistemaCajaBundle\Entity\Caja $caja
     * @return Habilitacion
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
     * Set usuario
     *
     * @param \Lar\UsuarioBundle\Entity\Usuario $usuario
     * @return Habilitacion
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

    /**
     * Set puesto
     *
     * @param \Caja\SistemaCajaBundle\Entity\Puesto $puesto
     * @return Habilitacion
     */
    public function setPuesto(\Caja\SistemaCajaBundle\Entity\Puesto $puesto = null)
    {
        $this->puesto = $puesto;
    
        return $this;
    }

    /**
     * Get puesto
     *
     * @return \Caja\SistemaCajaBundle\Entity\Puesto 
     */
    public function getPuesto()
    {
        return $this->puesto;
    }

    public function __toString(){
        return $this->getPuesto()->getDescripcion();
    }
}