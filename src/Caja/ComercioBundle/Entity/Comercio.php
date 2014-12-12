<?php

namespace Caja\ComercioBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * Comercio
 *
 * @ORM\Table(name="actividades")
 * *@ORM\Entity(repositoryClass="Caja\ComercioBundle\Entity\ComercioRepository")
 */
class Comercio
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
     * @ORM\Column(name="codigo", type="string", length=32)
     */
    private $codigo;

    /**
     * @var integer
     *
     * @ORM\Column(name="alicuota", type="integer")
     */
    private $alicuota;


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
     * Set codigo
     *
     * @param string $codigo
     * @return Comercio
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set alicuota
     *
     * @param integer $alicuota
     * @return Comercio
     */
    public function setAlicuota($alicuota)
    {
        $this->alicuota = $alicuota;

        return $this;
    }

    /**
     * Get alicuota
     *
     * @return integer
     */
    public function getAlicuota()
    {
        return $this->alicuota;
    }
}