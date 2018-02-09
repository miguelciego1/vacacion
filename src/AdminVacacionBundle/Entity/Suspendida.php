<?php

namespace AdminVacacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Suspendida
 *
 * @ORM\Table(name="vac_suspendida")
 * @ORM\Entity(repositoryClass="AdminVacacionBundle\Repository\SuspendidaRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Suspendida
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    //cvb

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaInicio", type="date")
     */
    private $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaFin", type="date")
     */
    private $fechaFin;

    /**
     * @var int
     *
     * @ORM\Column(name="dias", type="integer")
     */
    private $dias;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo", type="string", length=50)
     */
    private $motivo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creadoEl", type="datetime")
     */
    private $creadoEl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modificadoEl", type="datetime")
     */
    private $modificadoEl;

    /**
     * @var int
     *
     * @ORM\Column(name="estado", type="integer")
     */
    private $estado;

    /**
     * @var \AdminVacacionBundle\Entity\VacacionCabecera
     *
     * @ORM\ManyToOne(targetEntity="AdminVacacionBundle\Entity\VacacionCabecera")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vacacion_cabecera_id", referencedColumnName="id")
     * })
     */
    private $vacacionCabecera;


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
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     * @return Suspendida
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set fechaFin
     *
     * @param \DateTime $fechaFin
     * @return Suspendida
     */
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    /**
     * Get fechaFin
     *
     * @return \DateTime
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * Set dias
     *
     * @param integer $dias
     * @return Suspendida
     */
    public function setDias($dias)
    {
        $this->dias = $dias;

        return $this;
    }

    /**
     * Get dias
     *
     * @return integer
     */
    public function getDias()
    {
        return $this->dias;
    }

    /**
     * Set motivo
     *
     * @param string $motivo
     * @return Suspendida
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;

        return $this;
    }

    /**
     * Get motivo
     *
     * @return string
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

     /**
     * @ORM\PrePersist
     */
    public function setCreadoEl()
    {
        $this->creadoEl = new \DateTime();

        return $this;
    }

    /**
     * Get creadoEl
     *
     * @return \DateTime
     */
    public function getCreadoEl()
    {
        return $this->creadoEl;
    }

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     */
    public function setModificadoEl()
    {
        $this->modificadoEl = new \DateTime();

        return $this;
    }

    /**
     * Get modificadoEl
     *
     * @return \DateTime
     */
    public function getModificadoEl()
    {
        return $this->modificadoEl;
    }

    /**
     * Set estado
     *
     * @param integer $estado
     * @return Suspendida
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return integer
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set vacacionCabecera
     *
     * @param \AdminVacacionBundle\Entity\VacacionCabecera $vacacionCabecera
     * @return Suspendida
     */
    public function setVacacionCabecera(\AdminVacacionBundle\Entity\VacacionCabecera $vacacionCabecera = null)
    {
        $this->vacacionCabecera = $vacacionCabecera;

        return $this;
    }

    /**
     * Get vacacionCabecera
     *
     * @return \AdminVacacionBundle\Entity\VacacionCabecera
     */
    public function getVacacionCabecera()
    {
        return $this->vacacionCabecera;
    }
}
