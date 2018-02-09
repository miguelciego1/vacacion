<?php

namespace AdminVacacionBundle\Entity;


class PlanillaBaja
{

	private $empleado;
	private $desdeEl;
	private $hastaEl;
	private $sueldo;
	private $enfermedad;
	private $maternidad;
	private $accidente;
	private $riesgo;

	public function setEmpleado($empleado)
	{
		$this->empleado = $empleado;
	}

	public function getEmpleado()
	{
		return $this->empleado;
	}

	public function setDesdeEl($desdeEl)
	{
		$this->desdeEl = $desdeEl;
	}

	public function getDesdeEl()
	{
		return $this->desdeEl;

	}

	public function setHastaEl($hastaEl)
	{
		$this->hastaEl = $hastaEl;
	}

	public function getHastaEl()
	{
		return $this->hastaEl;
	}

	public function setSueldo($sueldo)
	{
		$this->sueldo = $sueldo;
	}

	public function getSueldo()
	{
		if ($this->sueldo) {
			return round($this->sueldo->getMonto() / 30, 2);
		} else {
			return 0;
		}
	}

	public function setEnfermedad($enfermedad)
	{
		$enfermedad==31?$this->enfermedad=30 :$this->enfermedad = $enfermedad;
	}

	public function getEnfermedad()
	{
		return $this->enfermedad;
	}

	public function setMaternidad($maternidad)
	{
        $maternidad==31?$this->maternidad=30 :$this->maternidad = $maternidad;
	}

	public function getMaternidad()
	{
		return $this->maternidad;
	}

	public function setAccidente($accidente)
	{
        $accidente==31?$this->accidente=30 :$this->accidente = $accidente;
	}

	public function getAccidente()
	{
		return $this->accidente;
	}

	public function setRiesgo($riesgo)
	{
        $riesgo==31?$this->riesgo=30 :$this->riesgo = $riesgo;
	}

	public function getRiesgo()
	{
		return $this->riesgo;
	}

	public function getSubsidio()
	{
		$valores = array('maternidad' => $this->maternidad, 'enfermedad' => $this->enfermedad, 'accidente' => $this->accidente, 'riesgo' => $this->riesgo);

		$tipo = array_search(max($valores), $valores);

		switch ($tipo) {
			case 'maternidad':
				if ($this->sueldo) {
					$subsidio = (($this->sueldo->getMonto() * 90) / 100);
					return round($subsidio / 30, 2);
				} else {
					return 0;
				}
				break;
			case 'enfermedad':
				if ($this->sueldo) {
					$subsidio = (($this->sueldo->getMonto() * 75) / 100);
					return round($subsidio / 30, 2);
				} else {
					return 0;
				}
				break;
			case 'accidente':
				if ($this->sueldo) {
					$subsidio = (($this->sueldo->getMonto() * 90) / 100);
					return round($subsidio / 30, 2);
				} else {
					return 0;
				}
				break;
			case 'riesgo':
				if ($this->sueldo) {
					$subsidio = (($this->sueldo->getMonto() * 75) / 100);
					return round($subsidio / 30, 2);
				} else {
					return 0;
				}
				break;
		}
	}


}
