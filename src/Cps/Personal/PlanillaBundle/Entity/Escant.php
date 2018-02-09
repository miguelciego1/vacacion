<?php

namespace Cps\Personal\PlanillaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="per_pla_escAnt")
 * @ORM\Entity()
 */
class Escant{
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="smallint", nullable=false)
     */
    private $desde;
    
    /**
     * @ORM\Column(type="smallint", nullable=false)
     */
    private $hasta;

    /**
     * @ORM\Column(type="smallint", nullable=false)
     */
    private $porcent;

// === Getter =============================================== //

    /**
     * @return integer 
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @return integer 
     */
    public function getDesde(){
        return $this->desde;
    }

    /**
     * @return integer 
     */
    public function getHasta(){
        return $this->hasta;
    }

    /**
     * @return integer 
     */
    public function getPorcent(){
        return $this->porcent;
    }
}
