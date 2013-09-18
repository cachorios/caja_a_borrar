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
     * @ORM\Column(name="lugar_ingreso", type="boolean")
     */
    private $lugar_ingreso;

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

    /*
     * validarIngreso
     * @param array $lugares
     * @return boolean $valido
     */
    public function validarIngreso($usuario) {

        $grupos = $usuario->getGrupos(); // Me fijo si el usuario pertenece al grupo de Administradores:
        foreach ($grupos as $grupo) {   //$usuario = $this->get('security.context')->isGranted('ROLE_ADMIN');
            if ($grupo->getNombre() == 'Administrador') {//Ok
                return true;
            }
        }
        /*
        $admin = $this->get  $this->get('security.context')->isGranted('ROLE_ADMIN');

        if ($admin)
            return true;
        */
        switch (date("w")) {
            case 0:
                $valido = $this->getDomingo();
                break;
            case 1:
                $valido = $this->getLunes();
                break;
            case 2:
                $valido = $this->getMartes();
                break;
            case 3:
                $valido = $this->getMiercoles();
                break;
            case 4:
                $valido = $this->getJueves();
                break;
            case 5:
                $valido = $this->getViernes();
                break;
            case 6:
                $valido = $this->getSabado();
                break;
        }
        //Si el dia es valido, verifico el horario:
        if ($valido || $this->getHorario()) {
            $desde = strtotime($this->getHorario()->getDesde());
            $hasta = strtotime($this->getHorario()->getHasta());
            $valido = strtotime(date('H:i:s')) >= $desde && strtotime(date('H:i:s')) <= $hasta;
        } else {
            return false; //Si no tiene horario de ingreso seteado, no puede entrar: error de datos:
        }
        //Verifico el lugar desde donde esta ingresando, para ver si se corresponde con lo que tiene asignado:
        if ($valido && $this->getLugarIngreso()) { // Tiene control de lugar
            /*
            $valido = false;
            foreach ($lugares as $lugar) {// recorro la lista de lugares permitidos:
                $mascara_valida = $lugar->getMascara();
                $mascara_ip_origen = substr($_SERVER['REMOTE_ADDR'], 0, strlen($mascara_valida));// Obtengo los primeros n caracteres de la ip que intenta ingresar:
                if ($mascara_valida == $mascara_ip_origen) {
                    return true; // esta todo bien
                }
            }
            */
        }
        return $valido;
    }

    /**
     * Set lugar_ingreso
     *
     * @param boolean $lugarIngreso
     * @return UsuarioIngreso
     */
    public function setLugarIngreso($lugarIngreso)
    {
        $this->lugar_ingreso = $lugarIngreso;

        return $this;
    }

    /**
     * Get lugar_ingreso
     *
     * @return boolean 
     */
    public function getLugarIngreso()
    {
        return $this->lugar_ingreso;
    }
}
