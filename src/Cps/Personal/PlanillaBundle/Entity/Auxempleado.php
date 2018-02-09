<?php

namespace Cps\Personal\PlanillaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="per_pla_auxempleado")
 * @ORM\Entity()
 */
class Auxempleado{

    public function __construct(){
        $this->sueldos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="ingresoEl" , type="date")
     */
    private $ingresoEl;

    /**
     * @ORM\Column( type="string", length=30)
     */
    private $cargo;

    /**
     * @ORM\Column(name="totGanado" ,type="decimal", scale=2)
     */
    private $totGanado;

    /**
     * @ORM\Column(type="smallint")
     */
    private $item;

    /**
     * @ORM\Column(name="carHoraria" , type="string", length=1)
     */
    private $carHoraria;

    /**
     * @ORM\Column(name="cenCosto" , type="string", length=1)
     */
    private $cenCosto;

// === Foraneas ======================================================== //

   /**
    * @ORM\OneToOne(targetEntity="Cps\Personal\ArchivoBundle\Entity\Empleado", inversedBy="auxempleado")
    * @ORM\JoinColumn(name="empleado_id", referencedColumnName="id")
    */
   protected $empleado;

//   , inversedBy="perPlaAuxEmp"

// === Funciones Auxiliares ============================================ //

    public function getIdNombreCompleto(){
        return $this->empleado->getIdNombreCompleto();
    }

// === RetroLLamadas =================================================== //

// === Getter and Setter =============================================== //

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
     * Set ingresoEl
     *
     * @param \DateTime $ingresoEl
     * @return Auxempleado
     */
    public function setIngresoEl($ingresoEl)
    {
        $this->ingresoEl = $ingresoEl;

        return $this;
    }

    /**
     * Get ingresoEl
     *
     * @return \DateTime
     */
    public function getIngresoEl()
    {
        return $this->ingresoEl;
    }

    /**
     * Set cargo
     *
     * @param string $cargo
     * @return Auxempleado
     */
    public function setCargo($cargo)
    {
        $this->cargo = $cargo;

        return $this;
    }

    /**
     * Get cargo
     *
     * @return string
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * Set totGanado
     *
     * @param string $totGanado
     * @return Auxempleado
     */
    public function setTotGanado($totGanado)
    {
        $this->totGanado = $totGanado;

        return $this;
    }

    /**
     * Get totGanado
     *
     * @return string
     */
    public function getTotGanado()
    {
        return $this->totGanado;
    }

    /**
     * Set item
     *
     * @param integer $item
     * @return Auxempleado
     */
    public function setItem($item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return integer
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set carHoraria
     *
     * @param string $carHoraria
     * @return Auxempleado
     */
    public function setCarHoraria($carHoraria)
    {
        $this->carHoraria = $carHoraria;

        return $this;
    }

    /**
     * Get carHoraria
     *
     * @return string
     */
    public function getCarHoraria()
    {
        return $this->carHoraria;
    }

    /**
     * Set cenCosto
     *
     * @param string $cenCosto
     * @return Auxempleado
     */
    public function setCenCosto($cenCosto)
    {
        $this->cenCosto = $cenCosto;

        return $this;
    }

    /**
     * Get cenCosto
     *
     * @return string
     */
    public function getCenCosto()
    {
        return $this->cenCosto;
    }

    /**
     * Set empleado
     *
     * @param \Cps\Personal\ArchivoBundle\Entity\Empleado $empleado
     * @return Auxempleado
     */
    public function setEmpleado(\Cps\Personal\ArchivoBundle\Entity\Empleado $empleado = null)
    {
        $this->empleado = $empleado;

        return $this;
    }

    /**
     * Get empleado
     *
     * @return \Cps\Personal\ArchivoBundle\Entity\Empleado
     */
    public function getEmpleado()
    {
        return $this->empleado;
    }
}
