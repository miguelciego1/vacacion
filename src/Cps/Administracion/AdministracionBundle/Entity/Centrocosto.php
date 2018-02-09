<?php

namespace Cps\Administracion\AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="adm_centroCosto",
              uniqueConstraints={@ORM\UniqueConstraint(name="duplicado_idx", columns={"sucursal_id", "ubicacion_id", "Servicio_id", "distintivo"})}
             )
 * @ORM\Entity(repositoryClass="Cps\Administracion\AdministracionBundle\Repository\CentrocostoRepository")
 */
class Centrocosto{

    public function __construct()
    {
    }


    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15, nullable=false)
     */
    private $distintivo;

    /**
     * @ORM\Column(name="siglaDis", type="string", length=5, nullable=false)
     */
    private $siglaDis;

// === Otras Funciones ================================================= //

    public function __toString(){
        return $this->id." "."-"." ".$this->sucursal->getNombre()." "."-"." ".$this->ubicacion->getNombre()." "."-"." ".$this->servicio->getNombre()." "."-"." ".$this->distintivo;
    }

    public function getNombre(){
        return $this->sucursal->getNombre().":".$this->ubicacion->getNombre().":".$this->servicio->getNombre();
    }

    public function getSigla(){
        return $this->sucursal->getSigla().":".$this->ubicacion->getSigla().":".$this->Servicio->getSigla();
    }

// === Foraneas ======================================================== //

   /**
    * @ORM\ManyToOne(targetEntity="Sucursal", inversedBy="centroCostos")
    * @ORM\JoinColumn(nullable=false)
    */
   protected $sucursal;

   /**
    * @ORM\ManyToOne(targetEntity="Ubicacion", inversedBy="centroCostos")
    * @ORM\JoinColumn(nullable=false)
    */
   protected $ubicacion;

   /**
    * @ORM\ManyToOne(targetEntity="Servicio", inversedBy="centroCostos")
    * @ORM\JoinColumn(nullable=false)
    */
   protected $servicio;

// === Getter ============================================================ //

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
     * Set distintivo
     *
     * @param string $distintivo
     * @return Centrocosto
     */
    public function setDistintivo($distintivo)
    {
        $this->distintivo = $distintivo;

        return $this;
    }

    /**
     * Get distintivo
     *
     * @return string
     */
    public function getDistintivo()
    {
        return $this->distintivo;
    }

    /**
     * Set siglaDis
     *
     * @param string $siglaDis
     * @return Centrocosto
     */
    public function setSiglaDis($siglaDis)
    {
        $this->siglaDis = $siglaDis;

        return $this;
    }

    /**
     * Get siglaDis
     *
     * @return string
     */
    public function getSiglaDis()
    {
        return $this->siglaDis;
    }

    /**
     * Set sucursal
     *
     * @param \Cps\Administracion\AdministracionBundle\Entity\Sucursal $sucursal
     * @return Centrocosto
     */
    public function setSucursal(\Cps\Administracion\AdministracionBundle\Entity\Sucursal $sucursal)
    {
        $this->sucursal = $sucursal;

        return $this;
    }

    /**
     * Get sucursal
     *
     * @return \Cps\Administracion\AdministracionBundle\Entity\Sucursal
     */
    public function getSucursal()
    {
        return $this->sucursal;
    }

    /**
     * Set ubicacion
     *
     * @param \Cps\Administracion\AdministracionBundle\Entity\Ubicacion $ubicacion
     * @return Centrocosto
     */
    public function setUbicacion(\Cps\Administracion\AdministracionBundle\Entity\Ubicacion $ubicacion)
    {
        $this->ubicacion = $ubicacion;

        return $this;
    }

    /**
     * Get ubicacion
     *
     * @return \Cps\Administracion\AdministracionBundle\Entity\Ubicacion
     */
    public function getUbicacion()
    {
        return $this->ubicacion;
    }

    /**
     * Set Servicio
     *
     * @param \Cps\Administracion\AdministracionBundle\Entity\Servicio $servicio
     * @return Centrocosto
     */
    public function setServicio(\Cps\Administracion\AdministracionBundle\Entity\Servicio $servicio)
    {
        $this->Servicio = $servicio;

        return $this;
    }

    /**
     * Get Servicio
     *
     * @return \Cps\Administracion\AdministracionBundle\Entity\Servicio
     */
    public function getServicio()
    {
        return $this->servicio;
    }

}
