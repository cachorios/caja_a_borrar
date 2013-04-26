<?php
/**
 * @author fito
 * @version: 11/04/13 18:49
 *
 */

namespace Common\AuditorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Auditoria
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Common\AuditorBundle\Entity\AuditoriaRepository")
 */
class Auditoria {
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
     * @var string
     *
     * @ORM\Column(name="usuario", type="string", length=255)
     */
    private $usuario;
    /**
     * @var string
     *
     * @ORM\Column(name="accion", type="string", length=255)
     */
    private $accion;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Auditoria
     */
    public function setFecha($fecha) {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha() {
        return $this->fecha;
    }

    /**
     * Set usuario
     *
     * @param string $usuario
     * @return Auditoria
     */
    public function setUsuario($usuario) {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string
     */
    public function getUsuario() {
        return $this->usuario;
    }

    /**
     * Set accion
     *
     * @param string $accion
     * @return Auditoria
     */
    public function setAccion($accion) {
        $this->accion = $accion;

        return $this;
    }

    /**
     * Get accion
     *
     * @return string
     */
    public function getAccion() {
        return $this->accion;
    }

    /**
     * Genera un registro de auditoria como debe ser.
     *
     * @param $usuario
     * @param $accion
     * @return $this
     */
    public function auditar($usuario, $accion) {
        $this->setFecha(new \DateTime());
        $this->setUsuario($usuario);
        $this->setAccion($accion);

        return $this;
    }
}