<?php

namespace Cps\Administracion\AdministracionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="adm_dptoBol")
 * @ORM\Entity()
 */
class Dptobol{

    public function __construct(){
        $this->empleados   = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $nombre;
    
    /**
     * @ORM\Column(type="string", length=3)
     */
    private $sigla;

// === Funciones Auxiliares ============================================ //
    
    public function __toString(){
        return $this->nombre;
    }
        
// === Foraneas ======================================================== //

    /**
     * @ORM\OneToMany(targetEntity="Cps\Personal\ArchivoBundle\Entity\Empleado", mappedBy="proDocId")
     */
    protected $empleados;

// === Getter ========================================================= //

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
    public function getEmpleados(){
        return $this->empleados;
    }
}
