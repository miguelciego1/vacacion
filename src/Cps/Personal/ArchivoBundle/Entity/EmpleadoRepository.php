<?php

namespace Cps\Personal\ArchivoBundle\Entity;

use Doctrine\ORM\EntityRepository;
use General\ComunBundle\Libreria\Filtrar;

class EmpleadoRepository extends EntityRepository{
    
    public function findAjaxValue($term){
        $em = $this->getEntityManager();
        $term = str_replace(' ', '%', $term);
        $consul = $em->createQuery('SELECT z FROM CpsPerArchivoBundle:Empleado z
                                        WHERE z.activo=1 AND z.busajax LIKE \'%'.$term.'%\'
                                 ');
        $resp = $consul->getResult();
        return $resp;
    }
    
    public function findAjaxValue2($term){
        $em = $this->getEntityManager();
        $term = str_replace(' ', '%', $term);
        $consul = $em->createQuery('SELECT z FROM CpsPerArchivoBundle:Empleado z
                                        WHERE z.busajax LIKE \'%'.$term.'%\'
                                 ');
        $resp = $consul->getResult();
        return $resp;
    }
}
