<?php

namespace Cps\Personal\PlanillaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sueldo
 *
 * @ORM\Table(name="per_pla_sueldo",indexes={ @ORM\Index(name="busqueda_sue_idx", columns={"anoMes","empleado_id","concepto_id"})
                      })
 * @ORM\Entity(repositoryClass="Cps\Personal\PlanillaBundle\Repository\SueldoRepository")
 */
class Sueldo
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
     * @ORM\Column(name="cantidad", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $cantidad;

    /**
     * @var string
     *
     * @ORM\Column(name="monto", type="decimal", precision=10, scale=2)
     */
    private $monto;

    /**
     * @var string
     *
     * @ORM\Column(name="anoMes", type="string", length=7)
     */
    private $anoMes;

    /**
     * Many Features have One Product.
     * @ORM\ManyToOne(targetEntity="Cps\Personal\ArchivoBundle\Entity\Empleado", inversedBy="sueldo",)
     * @ORM\JoinColumn(name="empleado_id", referencedColumnName="id",nullable=false)
     */
    private $empleado;

    /**
     * Many Features have One Product.
     * @ORM\ManyToOne(targetEntity="Cps\Personal\PlanillaBundle\Entity\Concepto", inversedBy="sueldo")
     * @ORM\JoinColumn(name="concepto_id", referencedColumnName="id",nullable=false)
     */
    private $concepto;



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
     * Set cantidad
     *
     * @param string $cantidad
     * @return Sueldo
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    
        return $this;
    }

    /**
     * Get cantidad
     *
     * @return string 
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return Sueldo
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;
    
        return $this;
    }

    /**
     * Get monto
     *
     * @return string 
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set anoMes
     *
     * @param string $anoMes
     * @return Sueldo
     */
    public function setAnoMes($anoMes)
    {
        $this->anoMes = $anoMes;
    
        return $this;
    }

    /**
     * Get anoMes
     *
     * @return string 
     */
    public function getAnoMes()
    {
        return $this->anoMes;
    }

    /**
     * Set empleado
     *
     * @param \Cps\Personal\ArchivoBundle\Entity\Empleado $empleado
     * @return Sueldo
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
