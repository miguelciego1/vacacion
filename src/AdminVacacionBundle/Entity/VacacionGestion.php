<?php

namespace AdminVacacionBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * VacacionGestion
 *
 * @ORM\Table(name="vac_vacacion_gestion")
 * @ORM\Entity(repositoryClass="AdminVacacionBundle\Repository\VacacionGestionRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *     fields={"gestion","tipo","empleado"},
 *     errorPath="gestion",
 *     message="Esta gestion ya existe"
 * )
 */
class VacacionGestion
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
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=1, nullable=false)
     */
    private $tipo;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=9)
     * @Assert\Regex("/-+/")
     *
     *
     * @ORM\Column(name="gestion", type="string", length=10)
     */
    private $gestion;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\LessThan(31)
     * @ORM\Column(name="dias", type="integer")
     */
    private $dias;

    /**
     * @var int
     * @Assert\NotBlank()
     * @ORM\Column(name="tomados", type="integer", nullable=true)
     */
    private $tomados;

    /**
     * @var int
     *
     * @ORM\Column(name="estado", type="integer")
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
     * Many VacacionGestion have One Empleado.
     * @ORM\ManyToOne(targetEntity="Cps\Personal\ArchivoBundle\Entity\Empleado")
     * @ORM\JoinColumn(name="empleado_id", referencedColumnName="id")
     */
    private $empleado;

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        // validamos que la fecha de inicio no sea domingo
        if ($this->tomados>$this->dias) {
            $context->buildViolation('Los tomados no tienen que ser mayor a sus disponibles')
                ->atPath('tomados')
                ->addViolation();

        }
    }

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
     * Set gestion
     *
     * @param string $gestion
     * @return VacacionGestion
     */
    public function setGestion($gestion)
    {
        $this->gestion = $gestion;

        return $this;
    }

    /**
     * Get gestion
     *
     * @return string
     */
    public function getGestion()
    {
        return $this->gestion;
    }

    /**
     * Set dias
     *
     * @param integer $dias
     * @return VacacionGestion
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
     * Set tomados
     *
     * @param integer $tomados
     * @return VacacionGestion
     */
    public function setTomados($tomados)
    {
        $this->tomados = $tomados;

        return $this;
    }

    /**
     * Get tomados
     *
     * @return integer
     */
    public function getTomados()
    {
        return $this->tomados;
    }

    /**
     * Set estado
     *
     * @param integer $estado
     * @return VacacionGestion
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
     * Set empleado
     *
     * @param \Cps\Personal\ArchivoBundle\Entity\Empleado $empleado
     * @return VacacionGestion
     */
    public function setEmpleado(\Cps\Personal\ArchivoBundle\Entity\Empleado $empleado = null)
    {
        $this->empleado = $empleado;

        return $this;
    }

    /**
     * Get empleado
     *
     * @return \AdminVacacionBundle\Entity\Empleado
     */
    public function getEmpleado()
    {
        return $this->empleado;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     * @return VacacionGestion
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    
        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return $this->tipo;
    }
}
