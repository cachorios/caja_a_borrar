<?php

namespace Caja\SistemaCajaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Responsable
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Caja\SistemaCajaBundle\Entity\ResponsableRepository")
 */
class Responsable
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
     * @ORM\Column(name="descripcion", type="string", length=64)
     */
    private $descripcion;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=96, unique=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="detalle", type="boolean", nullable = true)
     */
    private $detalle;

    /**
     * @var boolean
     *
     * @ORM\Column(name="resumen", type="boolean", nullable = true)
     */
    private $resumen;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean", nullable = true)
     */
    private $activo;

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
     * @return Responsable
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
     * Set email
     *
     * @param string $email
     * @return Responsable
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set detalle
     *
     * @param boolean $detalle
     * @return Responsable
     */
    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;

        return $this;
    }

    /**
     * Get detalle
     *
     * @return boolean 
     */
    public function getDetalle()
    {
        return $this->detalle;
    }

    /**
     * Set resumen
     *
     * @param boolean $resumen
     * @return Responsable
     */
    public function setResumen($resumen)
    {
        $this->resumen = $resumen;

        return $this;
    }

    /**
     * Get resumen
     *
     * @return boolean 
     */
    public function getResumen()
    {
        return $this->resumen;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return Responsable
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean 
     */
    public function getActivo()
    {
        return $this->activo;
    }
}