<?php

namespace AdminVacacionBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PermisoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PermisoRepository extends EntityRepository
{
	public function findByEmpleadoNoCargado($empleado)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery('
            SELECT p
            FROM AdminVacacionBundle:Permiso p
            WHERE p.descontado=:descontado
            AND p.empleado=:empleado
            AND p.estado NOT IN (:es,:ess)
            AND p.tipoPermiso=:tipoPermiso
            ')->setParameter('empleado', $empleado)
			->setParameter('tipoPermiso', 'V')
			->setParameter('es', 'U')
			->setParameter('ess', 'V')
			->setParameter('descontado', false);
		$permisos = $query->getResult();
		return $permisos;
	}

	public function findByEmpleadoNoCargadoCom($empleado, $fecha)
	{
		$em = $this->getEntityManager();
		$f = $fecha->format('Y-m-d H:i:s');
		$query = $em->createQuery('
            SELECT p
            FROM AdminVacacionBundle:Permiso p
            WHERE p.descontado=:descontado
            AND p.empleado=:empleado
            AND p.estado NOT IN (:es,:ess)
            AND p.tipoPermiso=:tipoPermiso
            AND p.creadoEl<:fecha
            ')->setParameter('empleado', $empleado)
			->setParameter('tipoPermiso', 'V')
			->setParameter('descontado', false)
			->setParameter('es', 'U')
			->setParameter('ess', 'V')
			->setParameter('fecha', $f);
		$permisos = $query->getResult();
		return $permisos;
	}


	public function findByFechas($fecIni, $fecFin, $empleadoId)
	{
		$em = $this->getEntityManager();
		if (empty($empleadoId)) {
			$query = $em->createQuery('
          SELECT p
          FROM AdminVacacionBundle:Permiso p
          WHERE p.fechaInicio BETWEEN :fecIni AND :fecFin
          OR p.fechaFin BETWEEN :fecIni AND :fecFin
          AND p.estado NOT IN (:es,:ess)
          AND p.tipoPermiso=:tipoPermiso')
				->setParameter('fecIni', $fecIni)
				->setParameter('tipoPermiso', 'V')
				->setParameter('es', 'U')
				->setParameter('ess', 'V')
				->setParameter('fecFin', $fecFin);
		} else {
			$query = $em->createQuery('
          SELECT p
          FROM AdminVacacionBundle:Permiso p
          WHERE (p.fechaInicio
          BETWEEN :fecIni AND :fecFin
          OR p.fechaFin BETWEEN :fecIni AND :fecFin)
          AND p.empleado=:empleado
          AND p.estado NOT IN (:es,:ess)
          AND p.tipoPermiso=:tipoPermiso')
				->setParameter('fecIni', $fecIni)
				->setParameter('fecFin', $fecFin)
				->setParameter('tipoPermiso', 'V')
				->setParameter('es', 'U')
				->setParameter('ess', 'V')
				->setParameter('empleado', $empleadoId);
		}


		$permisos = $query->getResult();

		return $permisos;
	}

	public function findDiasPermisosNoCargado($empleado)
	{
		$em = $this->getEntityManager();
		$permisosh = $em->createQuery(
			'SELECT sum(p.tiempoLicencia) as tiempoLicencia
        FROM AdminVacacionBundle:Permiso p
        where p.empleado=:empleado 
        and p.descontado=false
        AND p.estado NOT IN (:es,:ess)
        and p.tipoPermiso=:tipoPermiso
        and p.tipo=:tipo
        ')->setParameter('empleado', $empleado)
			->setParameter('tipo', 'H')
			->setParameter('tipoPermiso', 'V')
			->setParameter('es', 'U')
			->setParameter('ess', 'V')
			->getResult();

		$permisosd = $em->createQuery(
			'SELECT sum(p.tiempoLicencia) as tiempoLicencia
        FROM AdminVacacionBundle:Permiso p
        where p.empleado=:empleado
        and p.descontado=false
        AND p.estado NOT IN (:es,:ess)
        and p.tipoPermiso=:tipoPermiso
        and p.tipo=:tipo
        ')->setParameter('empleado', $empleado)
			->setParameter('tipo', 'D')
			->setParameter('es', 'U')
			->setParameter('ess', 'V')
			->setParameter('tipoPermiso', 'V')
			->getResult();

		if (is_null($permisosd)) {
			$diasD = 0;
		} else {
			$diasD = $permisosd[0]['tiempoLicencia'];
		}

		if (count($permisosh) > 0) {
			$diasH = $permisosh[0]['tiempoLicencia'];
			$diasH = $diasH / 7;
			$diasH = floor($diasH);
		} else {
			$diasH = 0;
		}
		return $diasH + $diasD;


	}

	public function findDiasPermisosCargado($idvacacion)
	{
		$em = $this->getEntityManager();
		$permisosh = $em->createQuery(
			'SELECT sum(p.tiempoLicencia) as tiempoLicencia
        FROM AdminVacacionBundle:Permiso p
        where p.vacacionCabecera=:vacacion
        and p.tipo=:tipo
        ')->setParameter('vacacion', $idvacacion)
			->setParameter('tipo', 'H')
			->getResult();

		$permisosd = $em->createQuery(
			'SELECT sum(p.tiempoLicencia) as tiempoLicencia
        FROM AdminVacacionBundle:Permiso p
        where p.vacacionCabecera=:vacacion
        and p.tipo=:tipo
        ')->setParameter('vacacion', $idvacacion)
			->setParameter('tipo', 'D')
			->getResult();

		if (is_null($permisosd)) {
			$diasD = 0;
		} else {
			$diasD = $permisosd[0]['tiempoLicencia'];
		}

		if (count($permisosh) > 0) {
			$diasH = $permisosh[0]['tiempoLicencia'];
			$diasH = $diasH / 7;
			$diasH = floor($diasH);
		} else {
			$diasH = 0;
		}
		return $diasH + $diasD;


	}
	public function actualizarMayorId($permiso,$empleado)
	{
		$em=$this->getEntityManager();

		$query=$em->createQuery(
			'UPDATE AdminVacacionBundle:Permiso p SET p.descontado=false
					WHERE p.vacacionCabecera is null 
					AND p.id>:permiso
					AND p.empleado=:empleado
            		AND p.estado NOT IN (:es,:ess)
            		AND p.tipoPermiso=:tipoPermiso')
			->setParameter('empleado',$empleado)
			->setParameter('es','V')
			->setParameter('ess','U')
			->setParameter('tipoPermiso','V')
			->setParameter('permiso',$permiso);
		$query->execute();
	}
	public function actualizarMenorId($permiso,$empleado)
	{
		$em=$this->getEntityManager();

		$query=$em->createQuery(
			'UPDATE AdminVacacionBundle:Permiso p SET p.descontado=TRUE 
					WHERE p.vacacionCabecera is null 
					AND p.id<=:permiso
					AND p.empleado=:empleado
            		AND p.estado NOT IN (:es,:ess)
            		AND p.tipoPermiso=:tipoPermiso')
			->setParameter('empleado',$empleado)
			->setParameter('es','V')
			->setParameter('ess','U')
			->setParameter('tipoPermiso','V')
			->setParameter('permiso',$permiso);
		$query->execute();
	}

}