<?php
namespace Lar\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * UsuarioIngreso
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class UsuarioIngreso
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
     * @ORM\OneToOne(targetEntity="\Lar\UsuarioBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     */
    private $usuario;


    /**
     * @ORM\ManyToOne(targetEntity="\Lar\UsuarioBundle\Entity\HorarioIngreso")

     * @ORM\JoinColumn(name="horario_id", referencedColumnName="id")
     */
    private $horario;

    /**
     * @var boolean
     *
     * @ORM\Column(name="lunes", type="boolean")
     */
    private $lunes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="martes", type="boolean")
     */
    private $martes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="miercoles", type="boolean")
     */
    private $miercoles;

    /**
     * @var boolean
     *
     * @ORM\Column(name="jueves", type="boolean")
     */
    private $jueves;

    /**
     * @var boolean
     *
     * @ORM\Column(name="viernes", type="boolean")
     */
    private $viernes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sabado", type="boolean")
     */
    private $sabado;

    /**
     * @var boolean
     *
     * @ORM\Column(name="domingo", type="boolean")
     */
    private $domingo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="control_lugar", type="boolean")
     */
    private $control_lugar;

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
     * Set lunes
     *
     * @param boolean $lunes
     * @return UsuarioIngreso
     */
    public function setLunes($lunes)
    {
        $this->lunes = $lunes;

        return $this;
    }

    /**
     * Get lunes
     *
     * @return boolean
     */
    public function getLunes()
    {
        return $this->lunes;
    }

    /**
     * Set martes
     *
     * @param boolean $martes
     * @return UsuarioIngreso
     */
    public function setMartes($martes)
    {
        $this->martes = $martes;

        return $this;
    }

    /**
     * Get martes
     *
     * @return boolean
     */
    public function getMartes()
    {
        return $this->martes;
    }

    /**
     * Set miercoles
     *
     * @param boolean $miercoles
     * @return UsuarioIngreso
     */
    public function setMiercoles($miercoles)
    {
        $this->miercoles = $miercoles;

        return $this;
    }

    /**
     * Get miercoles
     *
     * @return boolean
     */
    public function getMiercoles()
    {
        return $this->miercoles;
    }

    /**
     * Set jueves
     *
     * @param boolean $jueves
     * @return UsuarioIngreso
     */
    public function setJueves($jueves)
    {
        $this->jueves = $jueves;

        return $this;
    }

    /**
     * Get jueves
     *
     * @return boolean
     */
    public function getJueves()
    {
        return $this->jueves;
    }

    /**
     * Set viernes
     *
     * @param boolean $viernes
     * @return UsuarioIngreso
     */
    public function setViernes($viernes)
    {
        $this->viernes = $viernes;

        return $this;
    }

    /**
     * Get viernes
     *
     * @return boolean
     */
    public function getViernes()
    {
        return $this->viernes;
    }

    /**
     * Set sabado
     *
     * @param boolean $sabado
     * @return UsuarioIngreso
     */
    public function setSabado($sabado)
    {
        $this->sabado = $sabado;

        return $this;
    }

    /**
     * Get sabado
     *
     * @return boolean
     */
    public function getSabado()
    {
        return $this->sabado;
    }

    /**
     * Set domingo
     *
     * @param boolean $domingo
     * @return UsuarioIngreso
     */
    public function setDomingo($domingo)
    {
        $this->domingo = $domingo;

        return $this;
    }

    /**
     * Get domingo
     *
     * @return boolean
     */
    public function getDomingo()
    {
        return $this->domingo;
    }

    /**
     * Set control_lugar
     *
     * @param boolean $controlLugar
     * @return UsuarioIngreso
     */
    public function setControlLugar($controlLugar)
    {
        $this->control_lugar = $controlLugar;

        return $this;
    }

    /**
     * Get control_lugar
     *
     * @return boolean
     */
    public function getControlLugar()
    {
        return $this->control_lugar;
    }

    /**
     * Set usuario
     *
     * @param \Lar\UsuarioBundle\Entity\Usuario $usuario
     * @return UsuarioIngreso
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
     * Set horario
     *
     * @param \Lar\UsuarioBundle\Entity\HorarioIngreso $horario
     * @return UsuarioIngreso
     */
    public function setHorario(\Lar\UsuarioBundle\Entity\HorarioIngreso $horario = null)
    {
        $this->horario = $horario;

        return $this;
    }

    /**
     * Get horario
     *
     * @return \Lar\UsuarioBundle\Entity\HorarioIngreso
     */
    public function getHorario()
    {
        return $this->horario;
    }
}
