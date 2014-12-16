<?php

namespace Caja\ComercioBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Comercio
 *
 * @ORM\Table(name="djm_vista_cajas")
 * *@ORM\Entity(repositoryClass="Caja\ComercioBundle\Entity\ComercioRepository")
 */
class Comercio
{
   /**
     * @var integer
     *
     * @ORM\Column(name="comprobante", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="razon_social", type="string", length=200)
     */
    private $razonsocial;

    /**
     * @var string
     *
     * @ORM\Column(name="contribuyente", type="string", length=9)
     */
    private $contribuyente;

    /**
     * @var string
     *
     * @ORM\Column(name="periodos", type="string", length=512)
     */
    private $periodos;



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
     * Set razonsocial
     *
     * @param string $razonsocial
     * @return Comercio
     */
    public function setRazonsocial($razonsocial)
    {
        $this->razonsocial = $razonsocial;
    
        return $this;
    }

    /**
     * Get razonsocial
     *
     * @return string 
     */
    public function getRazonsocial()
    {
        return $this->razonsocial;
    }

    /**
     * Set contribuyente
     *
     * @param string $contribuyente
     * @return Comercio
     */
    public function setContribuyente($contribuyente)
    {
        $this->contribuyente = $contribuyente;
    
        return $this;
    }

    /**
     * Get contribuyente
     *
     * @return string 
     */
    public function getContribuyente()
    {
        return $this->contribuyente;
    }

    /**
     * Set periodos
     *
     * @param string $periodos
     * @return Comercio
     */
    public function setPeriodos($periodos)
    {
        $this->periodos = $periodos;
    
        return $this;
    }

    /**
     * Get periodos
     *
     * @return string 
     */
    public function getPeriodos()
    {
        return $this->periodos;
    }
}