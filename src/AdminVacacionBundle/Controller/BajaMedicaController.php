<?php

namespace AdminVacacionBundle\Controller;

use AdminVacacionBundle\Entity\BajaMedica;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AdminVacacionBundle\Form\BajaMedicaType;
use AdminVacacionBundle\Entity\PlanillaBaja;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PHPExcel_Style_Alignment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Bajamedica controller.
 *
 * @Route("bajamedica")
 */
class BajaMedicaController extends Controller
{
	/* FUNCIONES PRINCIPALES CON RUTA*/
	/**
	 * Lists all bajaMedica entities.
	 *
	 * @Route("/", name="bajamedica_index")
	 * @Method({"GET","POST"})
	 */
	public function indexAction(Request $request)
	{
		$empleadoForm = array('empleado' => null);
		$form = $this->createForm('AdminVacacionBundle\Form\BusquedaType', $empleadoForm);
		$session = $this->getRequest()->getSession();
		$form->handleRequest($request);
		$em = $this->getDoctrine()->getManager();

		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData();
			if ($form->get('buscar')->isClicked()) {
				$session->set('buscarE', $data['empleado']);
			} else {
				$session->set('buscarE', 0);
			}
		}


		if ($session->get('buscarE') > 0) {
			$query = $em->createQuery(
				'SELECT p
          FROM AdminVacacionBundle:BajaMedica p
          WHERE p.empleado=:empleado
          ORDER BY p.id DESC'
			)->setParameter('empleado', $session->get('buscarE'));
		} else {
			$query = $em->createQuery(
				'SELECT p
          FROM AdminVacacionBundle:BajaMedica p
          ORDER BY p.id DESC'
			);
		}


		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 15);

		return $this->render('AdminVacacionBundle:bajamedica:index.html.twig', array(
			'bajamedicas' => $pagination,
			'formBuscar' => $form->createView(),
		));
	}

	/**
	 * Creates a new bajaMedica entity.
	 *
	 * @Route("/new", name="bajamedica_new")
	 * @Method({"GET", "POST"})
	 */
	public function newAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$bajaMedica = new Bajamedica();
		$form = $this->createForm(new BajaMedicaType($em), $bajaMedica);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			if ($bajaMedica->getTipo() == 'X') {
				$fecha = strtotime('+44 day', strtotime($bajaMedica->getDesdeEl()));
				$bajaMedica->setHastaEl(date('Y-m-d', $fecha));
				$bajaMedica->setTipo('M');

			}
			$bajaMedica->setUsuario($this->getUser());
			$this->validarBajaMedica($bajaMedica, 0);
			$fechasPro = $this->calcularFecPro(0, $bajaMedica->getDesdeEl(), $bajaMedica->getHastaEl(), $bajaMedica->getEmpleado()->getId());
			$bajaMedica->setProDesdeEl($fechasPro['desdeEl']);
			$bajaMedica->setProHastaEl($fechasPro['hastaEl']);

			if ($this->validarSobrePosicion($bajaMedica)) {
				$this->get('ras_flash_alert.alert_reporter')->addError("Eror se detecto sobreposicion de fechas");
				return $this->redirectToRoute('bajamedica_new');
			}
			$em->persist($bajaMedica);
			$em->flush();
			$this->get('ras_flash_alert.alert_reporter')->addSuccess("Se registro correctamente la Baja Medica");
			return $this->redirectToRoute('bajamedica_edit', array('id' => $bajaMedica->getId()));
		}

		return $this->render('AdminVacacionBundle:bajamedica:new.html.twig', array(
			'bajaMedica' => $bajaMedica,
			'form' => $form->createView(),
		));
	}

	/**
	 * Displays a form to edit an existing bajaMedica entity.
	 *
	 * @Route("/{id}/edit", name="bajamedica_edit")
	 * @Method({"GET", "POST"})
	 */
	public function editAction(Request $request, BajaMedica $bajaMedica)
	{
		$em = $this->getDoctrine()->getManager();
		$editForm = $this->createForm(new BajaMedicaType($em), $bajaMedica);
		$editForm->handleRequest($request);

		if ($editForm->isSubmitted() && $editForm->isValid()) {
			if ($bajaMedica->getTipo() == 'X') {
				$fecha = strtotime('+44 day', strtotime($bajaMedica->getDesdeEl()));
				$bajaMedica->setHastaEl(date('Y-m-d', $fecha));
				$bajaMedica->setTipo('M');
			}
			$this->validarBajaMedica($bajaMedica, $bajaMedica->getId());
			$fechasPro = $this->calcularFecPro($bajaMedica->getId(), $bajaMedica->getDesdeEl(), $bajaMedica->getHastaEl(), $bajaMedica->getEmpleado()->getId());
			$bajaMedica->setProDesdeEl($fechasPro['desdeEl']);
			$bajaMedica->setProHastaEl($fechasPro['hastaEl']);
			$em->persist($bajaMedica);
			$em->flush();
			$this->get('ras_flash_alert.alert_reporter')->addSuccess("Se edito la Baja Medica");
			return $this->redirectToRoute('bajamedica_edit', array('id' => $bajaMedica->getId()));
		}

		return $this->render('AdminVacacionBundle:bajamedica:edit.html.twig', array(
			'bajaMedica' => $bajaMedica,
			'edit_form' => $editForm->createView(),
		));
	}

	/**
	 * Finds and displays a bajaMedica entity.
	 *
	 * @Route("/{id}", name="bajamedica_show")
	 * @Method("GET")
	 */
	public function showAction(BajaMedica $bajaMedica)
	{
		$deleteForm = $this->createDeleteForm($bajaMedica);

		return $this->render('bajamedica/show.html.twig', array(
			'bajaMedica' => $bajaMedica,
			'delete_form' => $deleteForm->createView(),
		));
	}

	/**
	 * Deletes a bajaMedica entity.
	 *
	 * @Route("/{id}/delete", name="bajamedica_delete")
	 * @Method("GET")
	 */
	public function deleteAction(BajaMedica $bajaMedica)
	{
		$em = $this->getDoctrine()->getManager();
		$em->remove($bajaMedica);
		$em->flush($bajaMedica);
		$this->get('ras_flash_alert.alert_reporter')->addSuccess("Se elimino la Baja Medica con correlativo :" . $bajaMedica->getId());
		return $this->redirectToRoute('bajamedica_index');
	}

	/**
	 * Creates a new bajaMedica entity.
	 *
	 * @Route("/planilla/", name="bajamedica_planilla")
	 * @Method({"GET", "POST"})
	 */
	public function generarPlanillaAux(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$formPlanilla = array('mes' => '', 'gestion' => '', 'opcion' => true);
		$form = $this->createForm('AdminVacacionBundle\Form\PlanillaType', $formPlanilla);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData();
			$my_date1 = new \DateTime();
			$my_date2 = new \DateTime();
			$fechaInicio = $my_date1->modify('first day of ' . $data['mes'] . ' ' . $data['gestion']);
			$fechaFin = $my_date2->modify('last day of ' . $data['mes'] . ' ' . $data['gestion']);
			//prueba
			$desdeEl = $fechaInicio->format('Y-m-d');
			$hastaEl = $fechaFin->format('Y-m-d');
			$opcion = $data['opcion'];
			//prueba

			$empleados = $em->getRepository('AdminVacacionBundle:BajaMedica')->traerEmpleadosParaPlanilla($desdeEl, $hastaEl);
			$bajasInvolucradas = $em->getRepository('AdminVacacionBundle:BajaMedica')->traerBajasParaPlanilla($desdeEl, $hastaEl);

			$planilla = array();

			foreach ($empleados as $empleado) {
				$empleadoPla = new PlanillaBaja();
				$empleadoPla->setEmpleado($empleado);
				//recorremos todos los empleado y buscamos las bajas medicas de ese mes
				$bajas = $em->getRepository('AdminVacacionBundle:BajaMedica')->traerBajasMedicasEmpleadoPlanilla($desdeEl, $hastaEl, $empleado['id']);
				$libre = 0;
				$ultima = new \DateTime();

				$enfermedad = 0;
				$maternidad = 0;
				$accidente = 0;
				$riesgo = 0;
				foreach ($bajas as $key => $baja) {
					$total = 0;
					if ($key == 0) {
						$empleadoPla->setDesdeEl($baja->getDesdeElObject());
						$añoMes = $baja->getDesdeElObject()->format('Y-m');

						$sueldo = $em->getRepository('CpsPerPlanillaBundle:Sueldo')->findOneBy(array('empleado' => $empleado['id'], 'concepto' => 47, 'anoMes' => $añoMes));

						$empleadoPla->setSueldo($sueldo);
					}

					$datosAux = $baja->getDatosAuxiliares($desdeEl, $hastaEl);
					$libre = $libre + $datosAux['ini'];
					$ultima = $baja->getHastaElObject();
					if ($libre >= 3) {

						$total = $total + $datosAux['cantidad'];

					} else {
						$saldo = 3 - $libre;
						if ($datosAux['cantidad'] >= $saldo) {
							$libre = 3;

							$total = $total + ($datosAux['cantidad'] - $saldo);
						} else {

							$libre = $libre + $datosAux['cantidad'];
						}

					}
					/*if ($baja->getEmpleado()->getId()==475) {
					  var_dump($total);
				  }*/
					switch ($baja->getTipo()) {
						case 'E':
							$enfermedad = $enfermedad + $total;
							break;
						case 'M':
							$maternidad = $maternidad + $total;
							break;
						case 'R':
							$riesgo = $riesgo + $total;
							break;
						default:
							$accidente = $accidente + $total;
							break;
					}


				}

                $valores = array( 'enfermedad' => $enfermedad,'maternidad' => $maternidad, 'accidente' => $accidente, 'riesgo' => $riesgo);
                $tipo = array_search(max($valores), $valores);

                switch ($tipo)
                {
                    case 'maternidad':
                        $maternidad=$maternidad+$accidente+$enfermedad+$riesgo;
                        $riesgo=0;
                        $enfermedad=0;
                        $accidente=0;
                        break;
                    case 'enfermedad':
                        $enfermedad=$maternidad+$accidente+$enfermedad+$riesgo;
                        $riesgo=0;
                        $maternidad=0;
                        $accidente=0;
                        break;
                    case 'accidente':
                        $accidente=$maternidad+$accidente+$enfermedad+$riesgo;
                        $riesgo=0;
                        $enfermedad=0;
                        $maternidad=0;
                        break;
                    case 'riesgo':
                        $riesgo=$maternidad+$accidente+$enfermedad+$riesgo;
                        $maternidad=0;
                        $enfermedad=0;
                        $accidente=0;
                        break;
                }



				$empleadoPla->setHastaEl($ultima);
				$empleadoPla->setEnfermedad($enfermedad);
				$empleadoPla->setAccidente($accidente);
				$empleadoPla->setMaternidad($maternidad);
				$empleadoPla->setRiesgo($riesgo);

				if ($empleadoPla->getEnfermedad() != 0 || $empleadoPla->getMaternidad() != 0 || $empleadoPla->getAccidente() != 0 || $empleadoPla->getRiesgo() != 0) {
					$planilla[] = $empleadoPla;
				}
			}

			if ($opcion) {
				return $this->render('AdminVacacionBundle:planilla:mostrar.html.twig', array(
					'planilla' => $planilla));
			} else {
				$response = $this->cargarExcel($planilla, $bajasInvolucradas, $desdeEl, $hastaEl);
				return $response;

			}
		}
		return $this->render('AdminVacacionBundle:planilla:new.html.twig', array(
			'formPlanilla' => $formPlanilla,
			'form' => $form->createView(),
		));

	}

	/**
	 * Creates a new bajaMedica entity.
	 *
	 * @Route("/planilla/procesar/", name="bajamedica_procesar")
	 * @Method({"GET", "POST"})
	 */
	public function procesarBajas(Request $request)
	{
		$desdeEl = '2017-09-30';
		$utilitario = new \AdminVacacionBundle\Utilitarios\Utilitario();
		$em = $this->getDoctrine()->getManager();
		$empleados = $em->getRepository('AdminVacacionBundle:BajaMedica')->traerEmpleadosParaProceso($desdeEl);

		foreach ($empleados as $empleado) {
			//recorremos todos los empleado y buscamos las bajas medicas de ese mes
			$bajas = $em->getRepository('AdminVacacionBundle:BajaMedica')->traerBajasMedicasEmpleado($desdeEl, $empleado['id']);
			$bajaAux = $em->getRepository('AdminVacacionBundle:BajaMedica')->traerBajaAux($desdeEl, $empleado['id']);

			if (!is_null($bajaAux)) {
				$estado = $bajaAux->getProHastaEl();
			} else {
				$estado = $desdeEl;
			}

			foreach ($bajas as $baja) {
				if ($baja->getDesdeEl() < $estado) {
					$fecha = strtotime('+1 day', strtotime($estado));
					$proDesdeEl = date('Y-m-d', $fecha);
					$fecha2 = strtotime('+' . $utilitario->obtenerDiferencia($baja->getHastaEl(), $baja->getDesdeEl()) . ' ' . 'day', strtotime($proDesdeEl));
					$proHastaEl = date('Y-m-d', $fecha2);
					$estado = $proHastaEl; // actualizamos el estado de la cola
					$baja->setProDesdeEl($proDesdeEl);
					$baja->setProHastaEl($proHastaEl);
					$baja->setEstado('P');
				} else {
					$baja->setProDesdeEl($baja->getDesdeEl());
					$baja->setProHastaEl($baja->getHastaEl());
					$baja->setEstado('P');
					$estado = $baja->getProHastaEl();
				}

				$em->flush($baja);
			}
		}
	}
	/* FIN DE FUNCIONES PRINCIPALES*/

	/* FUNCIOES DE APOYO*/
	public function validarSobrePosicion($baja)
	{
		$em = $this->getDoctrine()->getManager();
		if ($em->getRepository('AdminVacacionBundle:BajaMedica')->traerSobrePosicion($baja)) {
			return true;
		} else
			return false;
	}

	public function validarBajaMedica($bajaMedica, $opcion)
	{
		$em = $this->getDoctrine()->getManager();
		if ($bajaMedica->getTipo() == 'X' || $bajaMedica->getTipo() == 'M') {
			$bajamedica = $em->getRepository('AdminVacacionBundle:BajaMedica')->findUltimoBajaMaternidadEmpleado($bajaMedica->getEmpleado()->getId(), $bajaMedica->getDesdeEl(), $opcion);
			if (!is_null($bajamedica)) {
				$fechaS = $bajaMedica->getDesdeEl();
				$fecha = strtotime('-1 day', strtotime($fechaS));
				$bajamedica->setHastaEl(date('Y-m-d', $fecha));
				$bajamedica->setEstado('P');
				var_dump($bajamedica->getHastaEl());
				$em->flush();
			}
		}


	}

	public function calcularFecPro($id, $desdeEl, $hastaEl, $empleadoId)
	{
		$em = $this->getDoctrine()->getManager();
		$utilitario = new \AdminVacacionBundle\Utilitarios\Utilitario();

		if (date("j") <= 20) {
			$bajaProcesada = $em->getRepository('AdminVacacionBundle:BajaMedica')->findBajaProcesadaMes($empleadoId, $id);

			if (is_null($bajaProcesada)) {
				if (date('n') == date('n', strtotime($desdeEl))) {
					//si es null entonces se copia las fechas a fechas de procesado porque no se sobrepondran
					return array('desdeEl' => $desdeEl, 'hastaEl' => $hastaEl);
				} else {
					$proDesdeEl = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
					$proHastaEl = strtotime('+' . $utilitario->obtenerDiferencia($hastaEl, $desdeEl) . ' ' . 'day', strtotime($proDesdeEl));
					return array('desdeEl' => $proDesdeEl, 'hastaEl' => date('Y-m-d', $proHastaEl));
				}

			} else {
				//si no es null las fechas pueden cruzarse entonces en base al ultimo registro ($bajaProcesada) se calcula las fechas de procesado
				$fecha = strtotime('+1 day', strtotime($bajaProcesada->getProHastaEl()));
				$proDesdeEl = date('Y-m-d', $fecha);
				$proHastaEl = strtotime('+' . $utilitario->obtenerDiferencia($hastaEl, $desdeEl) . ' ' . 'day', strtotime($proDesdeEl));
				return array('desdeEl' => $proDesdeEl, 'hastaEl' => date('Y-m-d', $proHastaEl));
			}
		} else {
			$bajaProcesada = $em->getRepository('AdminVacacionBundle:BajaMedica')->findBajaProcesadaMesSiguiente($empleadoId, $id);
			if (is_null($bajaProcesada)) {
				// calculamos las fechas de procesada desde el 1 del mes siguiente
				$proDesdeEl = date('Y-m-d', mktime(0, 0, 0, date('m') + 1, 1, date('Y')));
				$proHastaEl = strtotime('+' . $utilitario->obtenerDiferencia($hastaEl, $desdeEl) . ' ' . 'day', strtotime($proDesdeEl));
				return array('desdeEl' => $proDesdeEl, 'hastaEl' => date('Y-m-d', $proHastaEl));
			} else {
				//si no es null las fechas pueden cruzarse entonces en base al ultimo registro ($bajaProcesada) se calcula las fechas de procesado
				$fecha = strtotime('+1 day', strtotime($bajaProcesada->getProHastaEl()));
				$proDesdeEl = date('Y-m-d', $fecha);
				$proHastaEl = strtotime('+' . $utilitario->obtenerDiferencia($hastaEl, $desdeEl) . ' ' . 'day', strtotime($proDesdeEl));
				return array('desdeEl' => $proDesdeEl, 'hastaEl' => date('Y-m-d', $proHastaEl));
			}
		}
	}

	public function cargarExcel($planilla, $bajas, $desdeEl, $hastaEl)
	{
		$mesesEs = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
		// solicitamos el servicio 'phpexcel' y creamos el objeto vacío...
		$phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
		$añoImp = date('Y', strtotime($desdeEl));
		$mesImp = $mesesEs[date('n', strtotime($desdeEl)) - 1];

		// ...y le asignamos una serie de propiedades
		$phpExcelObject->getProperties()
			->setCreator("Sistema Vacacion/Baja")
			->setLastModifiedBy("SVB")
			->setTitle("PLANILLA")
			->setSubject($mesImp . '/' . $añoImp)
			->setDescription("PLANILLA DE INCAPACIDAD DE " . $mesImp . '/' . $añoImp);


		// establecemos como hoja activa la primera, y le asignamos un título
		$phpExcelObject->setActiveSheetIndex(0);
		$phpExcelObject->getActiveSheet()->setTitle('PLANILLA');

		// establecemos como hoja activa la segunda, y le asignamos un título
		$objWorkSheet = $phpExcelObject->createSheet();
		$objWorkSheet->setTitle("BAJAS");

		// establecemos stylos por defecto y a celdas distintas
		$phpExcelObject->getDefaultStyle()->getFont()->setName('Arial')
			->setSize(9);

		//combinamos distintas celdas
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('A1:F1');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('M1:P1');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('A3:Q3');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('A4:Q4');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('A6:C6');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('D6:F6');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('A7:C7');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('D7:F7');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('A8:C8');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('D8:F8');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('A9:C9');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('D9:F9');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('A10:C10');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('D10:F10');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('A11:C11');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('D11:F11');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('A12:C12');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('D12:F12');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('A13:C13');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('D13:F13');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('B15:D15');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('J15:K15');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('L15:M15');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('N15:O15');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('P15:Q15');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('G8:H8');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('G9:H9');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('G10:H10');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('G11:H11');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('G12:H12');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('I6:I7');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('J6:J7');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('K8:L8');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('K9:L9');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('K6:L7');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('K10:L10');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('K11:L11');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('K12:L12');

		//ESTILOS PERSONALIZADOS
		$phpExcelObject->getActiveSheet()->getStyle('A1')->applyFromArray(array('font' => array('name' => 'Bookman Old Style', 'bold' => true, 'size' => 12, 'underline' => true, 'italic' => true)));
		$phpExcelObject->getActiveSheet()->getStyle('M1')->applyFromArray(array('font' => array('name' => 'Bookman Old Style', 'bold' => true, 'size' => 12, 'underline' => true, 'italic' => true)));
		$phpExcelObject->getActiveSheet()->getStyle('A3')->applyFromArray(array('font' => array('name' => 'Arial', 'bold' => true, 'size' => 13, 'underline' => true, 'italic' => true), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)));
		$phpExcelObject->getActiveSheet()->getStyle('A4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold' => true, 'size' => 13, 'underline' => true, 'italic' => true), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)));

		$phpExcelObject->getActiveSheet()->getStyle('A6:D13')->applyFromArray(array('font' => array('name' => 'Bookman Old Style', 'bold' => true, 'size' => 9), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)));

		// TITULOS DE LA PLANILLA
		$phpExcelObject->setActiveSheetIndex(0)
			->setCellValue('A1', 'JEFATURA DEPARTAMENTAL DE PERSONAL')
			->setCellValue('M1', 'SANTA CRUZ - BOLIVIA')
			->setCellValue('A3', 'PLANILLA DE SUBSIDIO DE INCAPACIDAD TEMPORAL')
			->setCellValue('A4', 'CAJA PETROLERA DE SALUD');

		//DATOPS GENERALES DE LA PLANILLA
		$phpExcelObject->setActiveSheetIndex(0)
			->setCellValue('A6', 'NOMBRE O RAZON SOCIAL DE LA EMPRESA')
			->setCellValue('D6', 'ADM REGIONAL')
			->setCellValue('A7', 'CAJA PETROLERA DE SALUD')
			->setCellValue('D7', 'SANTA CRUZ')
			->setCellValue('A8', 'DOMICILIO LEGAL')
			->setCellValue('D8', 'NUMERO PATRONAL')
			->setCellValue('A9', 'SANTA CRUZ')
			->setCellValue('D9', '730-7-049')
			->setCellValue('A10', 'LOCALIDAD CALLE N°')
			->setCellValue('D10', 'MES DE PLANILLA')
			->setCellValue('A11', 'SANTA CRUZ ESPAÑA/RAFAEL PEÑA')
			->setCellValue('D11', $mesImp . ' ' . $añoImp)
			->setCellValue('A12', 'RAMA DE ACTIVIDAD')
			->setCellValue('D12', 'TASA RIESGO PROF')
			->setCellValue('A13', 'SEGURO SOCIAL')
			->setCellValue('D13', '');

		//CABECERA DE LOS DATOS
		$phpExcelObject->setActiveSheetIndex(0)
			->setCellValue('B15', 'NOMBRE Y APELLIDO DEL TRABAJADOR')
			->setCellValue('E15', 'FEC. NACIMIENTO')
			->setCellValue('F15', 'FEC. BAJA')
			->setCellValue('G15', 'FEC. ALTA')
			->setCellValue('H15', 'SALARIO')
			->setCellValue('I15', 'SUBSIDIO')
			->setCellValue('J15', 'ENFERMEDAD')
			->setCellValue('L15', 'MATERNIDAD')
			->setCellValue('N15', 'ACCIDENTE DE TRABAJO')
			->setCellValue('P15', 'RIESGO PROFESIONAL')
			->setCellValue('A16', 'N°')
			->setCellValue('B16', 'PATERNO')
			->setCellValue('C16', 'MATERNO')
			->setCellValue('D16', 'NOMBRE')
			->setCellValue('E16', 'D/M/A')
			->setCellValue('F16', 'D/M')
			->setCellValue('G16', 'D/M')
			->setCellValue('H16', 'DIARIO')
			->setCellValue('I16', 'DIARIO')
			->setCellValue('J16', 'DIAS')
			->setCellValue('K16', 'MONTO')
			->setCellValue('L16', 'DIAS')
			->setCellValue('M16', 'MONTO')
			->setCellValue('N16', 'DIAS')
			->setCellValue('O16', 'MONTO')
			->setCellValue('P16', 'DIAS')
			->setCellValue('Q16', 'MONTO')
			->setCellValue('R16', 'ITEM ?');

		//DATOS DE LA PLANILLA
		$fila = 17;
		foreach ($planilla as $key => $empleado) {
			$item = 'SI';
			if ($empleado->getSueldo()) {
				$montoDiario = $empleado->getSueldo();
			} else {
				$montoDiario = 0;
				$item = 'NO';
			}
			$phpExcelObject->setActiveSheetIndex(0)
				->setCellValue('A' . $fila, $empleado->getEmpleado()['id'])
				->setCellValue('B' . $fila, $empleado->getEmpleado()['paterno'])
				->setCellValue('C' . $fila, $empleado->getEmpleado()['materno'])
				->setCellValue('D' . $fila, $empleado->getEmpleado()['nombre'])
				->setCellValue('E' . $fila, $empleado->getEmpleado()['fchnac']->format('d/m/Y'))
				->setCellValue('F' . $fila, $empleado->getDesdeEl()->format('d/m'))
				->setCellValue('G' . $fila, $empleado->getHastaEl()->format('d/m'))
				->setCellValue('H' . $fila, $montoDiario)
				->setCellValue('I' . $fila, $empleado->getSubsidio())
				->setCellValue('J' . $fila, $empleado->getEnfermedad())
				->setCellValue('K' . $fila, $empleado->getEnfermedad() * $montoDiario)
				->setCellValue('L' . $fila, $empleado->getMaternidad())
				->setCellValue('M' . $fila, $empleado->getMaternidad() * $montoDiario)
				->setCellValue('N' . $fila, $empleado->getAccidente())
				->setCellValue('O' . $fila, $empleado->getAccidente() * $montoDiario)
				->setCellValue('P' . $fila, $empleado->getRiesgo())
				->setCellValue('Q' . $fila, $empleado->getRiesgo() * $montoDiario)
				->setCellValue('R' . $fila, $item);
			$fila = $fila + 1;


			$phpExcelObject->setActiveSheetIndex(1)
				->setCellValue('A1', 'N°')
				->setCellValue('B1', 'N° Empl.')
				->setCellValue('C1', 'Tipo')
				->setCellValue('D1', 'Desde')
				->setCellValue('E1', 'Hasta')
				->setCellValue('F1', 'Dias');

			$fila2 = 2;
			foreach ($bajas as $baja) {
				$phpExcelObject->setActiveSheetIndex(1)
					->setCellValue('A' . $fila2, $baja->getId())
					->setCellValue('B' . $fila2, $baja->getEmpleado()->getId())
					->setCellValue('C' . $fila2, $baja->getTipo())
					->setCellValue('D' . $fila2, $baja->getProDesdeEl())
					->setCellValue('E' . $fila2, $baja->getProHastaEl())
					->setCellValue('F' . $fila2, $baja->getDatosAuxiliares($desdeEl, $hastaEl)['cantidad']);
				$fila2 = $fila2 + 1;
			}

			$phpExcelObject->setActiveSheetIndex(0)
				->setCellValue('J8', '=sum(J17:J' . $fila . ')')
				->setCellValue('J9', '=sum(L17:L' . $fila . ')')
				->setCellValue('J10', '=sum(N17:N' . $fila . ')')
				->setCellValue('J11', '=sum(P17:P' . $fila . ')')
				->setCellValue('K8', '=sum(K17:K' . $fila . ')')
				->setCellValue('K9', '=sum(M17:M' . $fila . ')')
				->setCellValue('K10', '=sum(O17:O' . $fila . ')')
				->setCellValue('K11', '=sum(Q17:Q' . $fila . ')');

			// DATOS TOTALES DE PLANILLA
			$phpExcelObject->setActiveSheetIndex(0)
				->setCellValue('G8', 'ENFERMEDAD')
				->setCellValue('G9', 'MATERNIDAD')
				->setCellValue('G10', 'ACCIDENTE DE TRABAJO')
				->setCellValue('G11', 'ENFERMEDAD. PROFESIONAL')
				->setCellValue('G12', 'TOTAL')
				->setCellValue('I6', 'N° TRABAJADORES INCAPACITADOS')
				->setCellValue('J6', 'N° DIAS INCAPACITADOS')
				->setCellValue('K6', 'MONTO PAGADO POR INCAPACIDAD TEMPORAL');

		}


		$hoy = date('d-m-Y');
		// se crea el writer
		$writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
		// se crea el response
		$response = $this->get('phpexcel')->createStreamedResponse($writer);
		// y por último se añaden las cabeceras
		$dispositionHeader = $response->headers->makeDisposition(
			ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'Planilla Incapacidad ' . $mesImp . '-' . $añoImp . '.xlsx'
		);
		$response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'maxage=1');
		$response->headers->set('Content-Disposition', $dispositionHeader);

		return $response;
	}
	/* FIN DE FUNCIONES DE APOYO */


}