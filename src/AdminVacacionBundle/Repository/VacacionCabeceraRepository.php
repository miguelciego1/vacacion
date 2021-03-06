<?php

namespace AdminVacacionBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * VacacionCabeceraRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VacacionCabeceraRepository extends EntityRepository
{
    public function findByFechas($fecIni,$fecFin,$empleadoId){
        $empleadoId=(int) $empleadoId;
        $em=$this->getEntityManager();
        if(empty($empleadoId)){

                return $em->createQuery('
                SELECT v
                FROM AdminVacacionBundle:VacacionCabecera v
                WHERE (v.fechaInicio BETWEEN :fecIni AND :fecFin
                OR v.fechaFin BETWEEN :fecIni AND :fecFin
                OR (v.fechaInicio < :fecIni AND v.fechaFin > :fecFin))
                AND v.anulado=:fal
                AND v.estado!=0
                ORDER BY v.id ASC')
                ->setParameter('fecIni',$fecIni)
                ->setParameter('fecFin',$fecFin)
                ->setParameter('fal',false)
                ->getResult();
        }
        else{
            return $em->createQuery('
                SELECT v
                FROM AdminVacacionBundle:VacacionCabecera v
                WHERE v.empleado=:empleado
                AND  (v.fechaInicio BETWEEN :fecIni AND :fecFin
                OR v.fechaFin BETWEEN :fecIni AND :fecFin)
                AND v.anulado=:fal
                AND v.estado!=0
                ORDER BY v.id ASC')
                ->setParameter('fecIni',$fecIni)
                ->setParameter('fecFin',$fecFin)
                ->setParameter('empleado',$empleadoId)
                ->setParameter('fal',false)
                ->getResult();
        }
    }
    public function findByFechasRegistro($fecIni,$fecFin){
        $em=$this->getEntityManager();
        return $em->createQuery('
                SELECT v
                FROM AdminVacacionBundle:VacacionCabecera v
                WHERE v.creadoEl BETWEEN :fecIni AND :fecFin
                AND v.anulado=:fal
                AND v.estado!=0
                ORDER BY v.id ASC')
                ->setParameter('fecIni',$fecIni)
                ->setParameter('fecFin',$fecFin)
                ->setParameter('fal',false)
                ->getResult();
        
    }

    public function findByEmpleadoSolicitud($empleadoId){
        $em=$this->getEntityManager();
                return $em->createQuery('
                SELECT v
                FROM AdminVacacionBundle:VacacionCabecera v
                WHERE v.empleado>=:empleado
                AND v.estado=1')
                ->setParameter('empleado',$empleadoId)
                ->getResult();
       
    }

    public function findByEmpleadoDisctinct($empleadoId){
        $em=$this->getEntityManager();
                return $em->createQuery('
                SELECT DISTINCT v
                FROM AdminVacacionBundle:VacacionCabecera v
                WHERE v.id>:corre
                AND v.tipo=:r
                AND v.empleado=:empleado')
                ->setParameter('empleado',$empleadoId)
                ->setParameter('corre',15341)
                ->setParameter('r','N')
                ->setMaxResults(1)
                ->getOneOrNullResult();
       
    }

	public function findByEmpleadoconfirmados($empleadoId){
		$em=$this->getEntityManager();
		return $em->createQuery('
                SELECT DISTINCT v
                FROM AdminVacacionBundle:VacacionCabecera v
                WHERE v.id>:corre
                AND v.tipo=:r
                AND v.empleado=:empleado')
			->setParameter('empleado',$empleadoId)
			->setParameter('corre',15341)
			->setParameter('r','N')
			->getResult();

	}




}
