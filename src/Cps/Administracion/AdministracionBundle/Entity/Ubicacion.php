<?php

namespace Cps\Administracion\AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ubicacion
 *
 * @ORM\Table(name="adm_ubicacion")
 * @ORM\Entity(repositoryClass="Cps\Administracion\AdministracionBundle\Repository\UbicacionRepository")
 */
class Ubicacion{

    public function __construct(){
        $this->centroCostos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $nombre;
    
    /**
     * @ORM\Column(type="string", length=10)
     */
    private $sigla;    
    
// === Foraneas ======================================================== // 

   /**
    * @ORM\OneToMany(targetEntity="Centrocosto", mappedBy="ubicacion")
    */
   protected $centroCostos;
   
// === Otras Funciones ================================================= //

    public function __toString(){
        return $this->nombre;
    }
    
// === Getter ========================================================== //

    /**
     * @return integer 
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @return string 
     */
    public function getNombre(){
        return $this->nombre;
    }

    /**
     * @return string 
     */
    public function getSigla(){
        return $this->sigla;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCentroCostos(){
        return $this->centroCostos;
    }

// ===================================================================== //

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Ubicacion
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Set sigla
     *
     * @param string $sigla
     * @return Ubicacion
     */
    public function setSigla($sigla)
    {
        $this->sigla = $sigla;

        return $this;
    }

    /**
     * Add centroCostos
     *
     * @param \Cps\Administracion\AdministracionBundle\Entity\Centrocosto $centroCostos
     * @return Ubicacion
     */
    public function addCentroCosto(\Cps\Administracion\AdministracionBundle\Entity\Centrocosto $centroCostos)
    {
        $this->centroCostos[] = $centroCostos;

        return $this;
    }

    /**
     * Remove centroCostos
     *
     * @param \Cps\Administracion\AdministracionBundle\Entity\Centrocosto $centroCostos
     */
    public function removeCentroCosto(\Cps\Administracion\AdministracionBundle\Entity\Centrocosto $centroCostos)
    {
        $this->centroCostos->removeElement($centroCostos);
    }
}
