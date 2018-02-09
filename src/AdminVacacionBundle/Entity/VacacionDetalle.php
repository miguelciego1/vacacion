<?php

namespace AdminVacacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VacacionDetalle
 *
 * @ORM\Table(name="vac_vacacion_detalle")
 * @ORM\Entity(repositoryClass="AdminVacacionBundle\Repository\VacacionDetalleRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class VacacionDetalle
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="dias", type="integer")
     */
    private $dias;

    /**
     * @ORM\Column(name="estado",type="integer")
     */
    private $estado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creado_el", type="datetime")
     */
    private $creadoEl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modificado_el", type="datetime")
     */
    private $modificadoEl;

    /**
     * Many VacacionDetalle have One VacacionGestion.
     * @ORM\ManyToOne(targetEntity="AdminVacacionBundle\Entity\VacacionGestion")
     * @ORM\JoinColumn(name="vacacion_gestion_id", referencedColumnName="id")
     */
     private $vacacionGestion;

     /**
     * Many VacacionDetalle have One VacacionCabecera.
     * @ORM\ManyToOne(targetEntity="AdminVacacionBundle\Entity\VacacionCabecera", inversedBy="vacacionDetalle")
     * @ORM\JoinColumn(name="vacacion_cabecera_id", referencedColumnName="id",onDelete="CASCADE")
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
     * Set dias
     *
     * @param integer $dias
     * @return VacacionDetalle
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
     * Set vacacionGestion
     *
     * @param \AdminVacacionBundle\Entity\VacacionGestion $vacacionGestion
     * @return VacacionDetalle
     */
    public function setVacacionGestion(\AdminVacacionBundle\Entity\VacacionGestion $vacacionGestion = null)
    {
        $this->vacacionGestion = $vacacionGestion;

        return $this;
    }

    /**
     * Get vacacionGestion
     *
     * @return \AdminVacacionBundle\Entity\VacacionGestion
     */
    public function getVacacionGestion()
    {
        return $this->vacacionGestion;
    }

    /**
     * Set vacacionCabecera
     *
     * @param \AdminVacacionBundle\Entity\VacacionCabecera $vacacionCabecera
     * @return VacacionDetalle
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

    /**
     * Set estado
     *
     * @param integer $estado
     * @return VacacionDetalle
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
}
