<?php

namespace AdminVacacionBundle\Controller;

use AdminVacacionBundle\Entity\VacacionCabecera;
use AdminVacacionBundle\Form\VacacionCabeceraType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AdminVacacionBundle\Entity\VacacionDetalle;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PHPExcel_Style_Alignment;

/**
 * Vacacioncabecera controller.
 *
 * @Route("vc")
 */
class VacacionCabeceraController extends Controller
{
	/**
	 * Lists all vacacionCabecera entities.
	 *
	 * @Route("/", name="vacacioncabecera_index_admin")
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
				'SELECT v
          FROM AdminVacacionBundle:VacacionCabecera v
          WHERE v.empleado=:empleado
          AND v.anulado=false
          ORDER BY v.id DESC'
			)->setParameter('empleado', $session->get('buscarE'));
		} else {
			$query = $em->createQuery(
				'SELECT v
          FROM AdminVacacionBundle:VacacionCabecera v
          WHERE v.anulado=false
          ORDER BY v.id DESC
          ');
		}


		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 15);

		return $this->render('AdminVacacionBundle:vacacioncabecera:index.html.twig', array(
			'vacacionCabeceras' => $pagination,
			'formBuscar' => $form->createView(),
		));
	}
	/**
	 * Creates a new vacacionCabecera entity.
	 *
	 * @Route("/new/solicitud", name="vacacioncabecera_new_admin")
	 * @Method({"GET", "POST"})
	 */
	public function newAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$vacacionCabecera = new Vacacioncabecera();
		$form = $this->createForm(new VacacionCabeceraType($em), $vacacionCabecera);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$empleadoId = $vacacionCabecera->getEmpleado()->getId();
			$vacacionGestiones = $em->getRepository('AdminVacacionBundle:VacacionGestion')->findVacacionGestionDisponibleByEmpleado($empleadoId, $vacacionCabecera->getTipo());
			$diasDisponibles = $em->getRepository('AdminVacacionBundle:VacacionGestion')->findByDiasDisponibles($empleadoId, $vacacionCabecera->getTipo());
			$diasPermisos = $em->getRepository('AdminVacacionBundle:Permiso')->findDiasPermisosNoCargado($empleadoId);

			$vacacionC = $em->getRepository('AdminVacacionBundle:VacacionCabecera')->findOneBy(array('empleado' => $empleadoId, 'estado' => 1));

			if (count($vacacionC) > 0) {
				$this->get('ras_flash_alert.alert_reporter')->addError("Ya tiene una solicitud de vacacion pendiente");
				return $this->redirectToRoute('vacacioncabecera_new_admin');
			}

			//Valido si el empleado tiene dias disponibkles para tomar la vacacion
			if ($vacacionCabecera->getTipo() == "N") {
				if ($vacacionCabecera->getTotalDias() + $diasPermisos > $diasDisponibles[0]['diasDisponibles']) {
					$total = $diasDisponibles[0]['diasDisponibles'] - $diasPermisos;
					$this->get('ras_flash_alert.alert_reporter')->addError("Los dias solicitados sobrepasan sus dias disponibles" . " " . "=" . $total);
					return $this->redirectToRoute('vacacioncabecera_new_admin');
				}
			} elseif ($vacacionCabecera->getTipo() == "R") {
				if ($vacacionCabecera->getTotalDias() > $diasDisponibles[0]['diasDisponibles']) {
					$this->get('ras_flash_alert.alert_reporter')->addError("Los dias solicitados sobrepasan sus dias disponibles");
					return $this->redirectToRoute('vacacioncabecera_new_admin');
				}
			}


			//Cargo el objeto de la cabecera de la vacacion
			$dias = $vacacionCabecera->getTotalDias() - 1;
			$feriados = $em->getRepository('CpsAdministracionBundle:Feriado')->findAll();
			$fechaV = $vacacionCabecera->getFechaInicio()->format('Y-m-d');
			if (date("w", strtotime($fechaV)) == 0) {
				$this->get('ras_flash_alert.alert_reporter')->addError("Su fecha de Inicio no puede ser fin de semana");
				return $this->redirectToRoute('vacacioncabecera_new_admin');
			} else {
				foreach ($feriados as $feriado) {
					$fechaFeriado = $feriado->getFecha()->format('Y-m-d');
					$dataFecha = explode("-", $fechaV);
					$dataFechaFeriado = explode("-", $fechaFeriado);

					if ($fechaV == $fechaFeriado) {
						$this->get('ras_flash_alert.alert_reporter')->addError("Su fecha de Inicio no puede ser FERIADO");
						return $this->redirectToRoute('vacacioncabecera_new_admin');
					}
				}
			}


			$vacacionCabecera->setFechaFin($this->get('cps_user_utilitario')
				->fechaFin($vacacionCabecera->getFechaInicio(), $dias, $feriados));
			$vacacionCabecera->setFechaRegreso($this->get('cps_user_utilitario')
				->fechaFin($vacacionCabecera->getFechaFin(), 1, $feriados));
			$vacacionCabecera->setEstado(1);
			$vacacionCabecera->setAnulado(false);
			$vacacionCabecera->setUsuario($this->getUser());
			$em->getConnection()->beginTransaction();

			try { //cargatr y ´persist los detalles y la cebecera de la cabecera
				$vacacionDetalles = $this->guardarDetalle($vacacionCabecera, $vacacionGestiones);
				foreach ($vacacionDetalles as $vacacionDetalle) {
					$em->persist($vacacionDetalle);
				}
				$em->persist($vacacionCabecera);
				$em->flush($vacacionCabecera);
				$this->get('ras_flash_alert.alert_reporter')->addSuccess("Se guardo correctamente la solicitud de vacacion");
				$em->getConnection()->commit();
				return $this->redirectToRoute('vacacioncabecera_index_admin');
			} catch (Exception $e) {
				$em->getConnection()->rollback();
				$this->get('ras_flash_alert.alert_reporter')->addError("No se pudo guardar la solicitud  de vacacion");
			}
		}

		return $this->render('AdminVacacionBundle:vacacioncabecera:new.html.twig', array(
			'vacacionCabecera' => $vacacionCabecera,
			'form' => $form->createView(),
		));
	}
	/**
	 * Displays a form to edit an existing ingreso entity.
	 *
	 * @Route("/{id}/edit", name="vacacion_cabecera_edit")
	 * @Method({"GET", "POST"})
	 */
	public function editAction(Request $request, VacacionCabecera $vacacionCabecera)
	{
		$em = $this->getDoctrine()->getManager();
		$form = $this->createForm(new VacacionCabeceraType($em), $vacacionCabecera);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$empleadoId = $vacacionCabecera->getEmpleado()->getId();

			$vacacionGestiones = $em->getRepository('AdminVacacionBundle:VacacionGestion')->findVacacionGestionDisponibleByEmpleado($empleadoId, $vacacionCabecera->getTipo());
			$diasDisponibles = $em->getRepository('AdminVacacionBundle:VacacionGestion')->findByDiasDisponibles($empleadoId, $vacacionCabecera->getTipo());
			$diasPermisos = $em->getRepository('AdminVacacionBundle:Permiso')->findDiasPermisosNoCargado($empleadoId);


			//Valido si el empleado tiene dias disponibles para tomar la vacacion
			if ($vacacionCabecera->getTipo() == "N") {
				if ($vacacionCabecera->getTotalDias() + $diasPermisos > $diasDisponibles[0]['diasDisponibles']) {
					$this->get('ras_flash_alert.alert_reporter')->addError("Los dias solicitados sobrepasan sus dias disponibles");
					return $this->redirectToRoute('vacacioncabecera_new_admin');
				}
			} elseif ($vacacionCabecera->getTipo() == "R") {
				if ($vacacionCabecera->getTotalDias() > $diasDisponibles[0]['diasDisponibles']) {
					$this->get('ras_flash_alert.alert_reporter')->addError("Los dias solicitados sobrepasan sus dias disponibles");
					return $this->redirectToRoute('vacacioncabecera_new_admin');
				}
			}

			//Cargo el objeto de la cabecera de la vacacion
			$dias = $vacacionCabecera->getTotalDias() - 1;
			$feriados = $em->getRepository('CpsAdministracionBundle:Feriado')->findAll();
			$fechaV = $vacacionCabecera->getFechaInicio()->format('Y-m-d');
			if (date("w", strtotime($fechaV)) == 0) {
				$this->get('ras_flash_alert.alert_reporter')->addError("Su fecha de Inicio no puede ser fin de semana");
				return $this->redirectToRoute('vacacioncabecera_new_admin');
			} else {
				foreach ($feriados as $feriado) {
					$fechaFeriado = $feriado->getFecha()->format('Y-m-d');
					$dataFecha = explode("-", $fechaV);
					$dataFechaFeriado = explode("-", $fechaFeriado);
					if ($fechaV == $fechaFeriado) {
						$this->get('ras_flash_alert.alert_reporter')->addError("Su fecha de Inicio no puede ser FERIADO");
						return $this->redirectToRoute('vacacioncabecera_new_admin');
					}
				}
			}

			$vacacionCabecera->setFechaFin($this->get('cps_user_utilitario')
				->fechaFin($vacacionCabecera->getFechaInicio(), $dias, $feriados));
			$vacacionCabecera->setFechaRegreso($this->get('cps_user_utilitario')
				->fechaFin($vacacionCabecera->getFechaFin(), 1, $feriados));
			$vacacionCabecera->setEstado(1);
			$vacacionCabecera->setUsuario($this->getUser());
			$detallesAnt = $em->getRepository('AdminVacacionBundle:VacacionDetalle')->findByVacacionCabecera($vacacionCabecera);
			$em->getConnection()->beginTransaction();

			try { //cargatr y ´persist los detalles y la cebecera de la cabecera

				$vacacionDetalles = $this->guardarDetalle($vacacionCabecera, $vacacionGestiones);
				foreach ($vacacionDetalles as $vacacionDetalle) {
					$em->persist($vacacionDetalle);
				}
				foreach ($detallesAnt as $value) {
					$em->remove($value);
				}

				$em->persist($vacacionCabecera);
				$em->flush();
				$this->get('ras_flash_alert.alert_reporter')->addSuccess("Se Edito correctamente la solicitud de vacacion");
				$em->getConnection()->commit();
				return $this->redirectToRoute('vacacioncabecera_index_admin');
			} catch (Exception $e) {
				$em->getConnection()->rollback();
				$this->get('ras_flash_alert.alert_reporter')->addError("No se pudo Editar la solicitud  de vacacion");
			}
		}

		return $this->render('AdminVacacionBundle:vacacioncabecera:edit.html.twig', array(
			'edit_form' => $form->createView(),
		));


	}

	/**
	 * Finds and displays a VacacionCabecera entity.
	 *
	 * @Route("/{id}", name="vacacioncabecera_show_admin")
	 * @Method("GET")
	 */
	public function showAction(VacacionCabecera $vacacionCabecera)
	{
		$em = $this->getDoctrine()->getManager();

		$vacacionDetalles = $em->getRepository('AdminVacacionBundle:VacacionDetalle')->findByVacacionCabecera($vacacionCabecera);

		$suspendidas = $em->getRepository('AdminVacacionBundle:Suspendida')->findByVacacionCabecera($vacacionCabecera);
		$permisos = $em->getRepository('AdminVacacionBundle:Permiso')->findByVacacionCabecera($vacacionCabecera);
		$empleado = $vacacionCabecera->getEmpleado();
		if ($vacacionCabecera->getEstado() == 1) {
			$totalDiasPermiso = $em->getRepository('AdminVacacionBundle:Permiso')->findDiasPermisosNoCargado($vacacionCabecera->getEmpleado()->getId());
		} else {
			$totalDiasPermiso = $this->get('cps_user_utilitario')->calcularTotalDias($permisos);
		}


		return $this->render('AdminVacacionBundle:vacacioncabecera:show.html.twig', array(
			'vacacionCabecera' => $vacacionCabecera,
			'vacacionDetalles' => $vacacionDetalles,
			'permisos' => $permisos,
			'empleado' => $empleado,
			'totalDiasPermiso' => $totalDiasPermiso,
			'suspendidas' => $suspendidas,
		));
	}


	/**
	 * Deletes a vacacionCabecera entity.
	 *
	 * @Route("/{id}/delete", name="vacacioncabecera_delete_admin")
	 * @Method({"GET","POST"})
	 */
	public function deleteAction(Request $request, VacacionCabecera $vacacionCabecera)
	{
		$em = $this->getDoctrine()->getManager();
		$permisos = $em->getRepository('AdminVacacionBundle:Permiso')->findByVacacionCabecera($vacacionCabecera);
		if ($vacacionCabecera->getTipo("N")) {
			$this->actualizarPermisoGestionVacacionEliminada($permisos, $vacacionCabecera->getEmpleado());
		}
		$vacacionCabecera->setAnulado(true);
		$vacacionCabecera->setEstado(2);
		$em->persist($vacacionCabecera);
		$em->flush();
		return $this->redirectToRoute('vacacioncabecera_index_admin');
	}

	public function actualizarPermisoGestionVacacionEliminada($permisos, $empleado)
	{
		$totalDiasPermiso = $this->get('cps_user_utilitario')->calcularTotalDias($permisos);
		$em = $this->getDoctrine()->getManager();
		$vacacionGestiones = $em->getRepository('AdminVacacionBundle:VacacionGestion')->findVacacionGestionDisponibleByEmpleado($empleado, "N");
		foreach ($vacacionGestiones as $vacacionGestion) {
			if ($totalDiasPermiso !== 0) {
				if ($vacacionGestion->getTomados() !== 0) {
					if ($vacacionGestion->getTomados() > $totalDiasPermiso) {
						$vacacionGestion->setTomados($vacacionGestion->getTomados() - $totalDiasPermiso);
						$em->persist($vacacionGestion);
					} else {
						$totalDiasPermiso = $totalDiasPermiso - $vacacionGestion->getTomados();
						$vacacionGestion->setTomados(0);
						$em->persist($vacacionGestion);

					}
				}
			}
		}

		foreach ($permisos as $per) {
			$per->setEstado(3);
			$per->setVacacionCabecera(NULL);
			$em->persist($per);
		}

		$em->flush();
	}

	/**
	 * @Route("/confirmar/{id}", name="vacacioncabecera_confirm")
	 * @Method({"GET"})
	 *
	 */
	public function confirmarSolicitud(VacacionCabecera $vacacionCabecera)
	{
		$em = $this->getDoctrine()->getManager();
		$fecha = $vacacionCabecera->getModificadoEl();
		$vacacionDetalles = $vacacionCabecera->getVacacionDetalle();
		foreach ($vacacionDetalles as $detalle) {
			$detalle->setEstado(2);
			$detalle->getVacacionGestion()->setTomados($detalle->getDias() + $detalle->getVacacionGestion()->getTomados());
		}
		$vacacionCabecera->setEstado(2);
		$em->persist($vacacionCabecera);
		$em->flush();
		$this->get('ras_flash_alert.alert_reporter')->addSuccess("Se confirmo correctamente la solicitud de vacacion");
		if ($vacacionCabecera->getTipo("N")) {
			$this->cargarPermisos($vacacionCabecera, $fecha);
		}

		return $this->redirectToRoute('vacacioncabecera_index_admin');
	}


	public function cargarPermisos($vacacionCabecera, $fecha)
	{
		$em = $this->getDoctrine()->getManager();
		$vacacionGestiones = $em->getRepository('AdminVacacionBundle:VacacionGestion')->findVacacionGestionDisponibleByEmpleado($vacacionCabecera->getEmpleado()->getId(), "N");
		$permisos = $em->getRepository('AdminVacacionBundle:Permiso')->findByEmpleadoNoCargadoCom($vacacionCabecera->getEmpleado()->getId(), $fecha);
		$permisoHoras = array();
		foreach ($permisos as $permiso) {
			if ($permiso->getTipo() == 'D') {
				$tiempoLicencia = $permiso->getTiempoLicencia();
				foreach ($vacacionGestiones as $vacacionGestion) {

					$diferencia = $vacacionGestion->getDias() - $vacacionGestion->getTomados();

					if ($tiempoLicencia > $diferencia) {
						$vacacionGestion->setTomados($vacacionGestion->getDias());
						$tiempoLicencia = $tiempoLicencia - $diferencia;
						$permiso->setDescontado(true);
						$em->persist($vacacionGestion);
					} else {
						$vacacionGestion->setTomados($vacacionGestion->getTomados() + $tiempoLicencia);
						$permiso->setVacacionCabecera($vacacionCabecera);
						$permiso->setDescontado(true);
						$tiempoLicencia = 0;
						$em->persist($permiso);
						$em->persist($vacacionGestion);
						break;
					}

				}
			} else {
				$permisoHoras[] = $permiso;
			}
		}
		if (count($permisoHoras) !== 0) {
			$result = $this->get('cps_user_utilitario')->cargaHorasPermiso($permisoHoras);
			$permisosH = $result[0];
			$diasH = $result[1];

			foreach ($vacacionGestiones as $vacacionGestion) {
				$diferencia = $vacacionGestion->getDias() - $vacacionGestion->getTomados();
				if ($diferencia > $diasH) {
					$vacacionGestion->setTomados($vacacionGestion->getTomados() + $diasH);
					$em->persist($vacacionGestion);
					break;
				} else {
					$diasH = $diasH - $diferencia;
					$vacacionGestion->setTomados($vacacionGestion->getDias());
					$em->persist($vacacionGestion);
				}
			}

			foreach ($permisosH as $perH) {
				$perH->setDescontado(true);
				$perH->setVacacionCabecera($vacacionCabecera);
				$em->persist($perH);
			}

		}

		$em->flush();


	}

	/**
	 * Export to PDF
	 *
	 * @Route("/{id}/pdf", name="vacacioncabecera_print_admin")
	 */
	public function pdfAction(VacacionCabecera $vacacionCabecera)
	{
		$em = $this->getDoctrine()->getManager();
		$query = $em->createQuery(
			'SELECT d
            FROM AdminVacacionBundle:VacacionDetalle d
            WHERE d.vacacionCabecera=:cabecera'
		)->setParameter('cabecera', $vacacionCabecera->getId());
		$vacacionDetalles = $query->getResult();

		$permisos = $em->getRepository('AdminVacacionBundle:Permiso')->findByEmpleadoNoCargado($vacacionCabecera->getEmpleado()->getId());

		$totalDiasPermiso = $this->get('cps_user_utilitario')->calcularTotalDias($permisos);
		$auxEmpleado = $em->getRepository('CpsPerPlanillaBundle:Auxempleado')->findOneByEmpleado($vacacionCabecera->getEmpleado()->getId());
		$centroCostos = $em->getRepository('CpsAdministracionBundle:Centrocosto')->findOneById($vacacionCabecera->getCentroCostos()->getId());
		$usuario = "Usuario:" . " " . $vacacionCabecera->getUsuario()->getLogin();

		$html = $this->renderView('AdminVacacionBundle:vacacioncabecera:solicitud.html.twig', array(
			'auxEmpleado' => $auxEmpleado,
			'vacacionCabecera' => $vacacionCabecera,
			'centroCostos' => $centroCostos,
			'vacacionDetalles' => $vacacionDetalles,
			'totalDiasPermiso' => $totalDiasPermiso
		));

		$filename = sprintf('SolicitudVacacion-%s.pdf', date('Y-m-d'));

		$response = new Response(
			$this->get('knp_snappy.pdf')->getOutputFromHtml($html,
				array('lowquality' => false,
					'encoding' => 'utf-8',
					'page-size' => 'legal',
					'outline-depth' => 8,
					'orientation' => 'Portrait',
					'title' => 'Solicitud de vacacion',
					'copies' => 1,
					'header-right' => $usuario,
					'outline' => true,
					'print-media-type' => true,
					'exclude-from-outline' => false,
					'images' => true,
					'user-style-sheet' => 'gentelella/vendors/bootstrap/dist/css/bootstrap.min.css',
					'header-font-size' => 7
				)),
			200,
			array(
				'Content-Type' => 'application/pdf',
				'Content-Disposition' => sprintf('inline; filename="%s"', $filename),
			)
		);
		return $response;
	}

	/**
	 * Export to Excel
	 *
	 * @Route("/{id}/excel", name="vacacioncabecera_excel_admin")
	 */
	public function exportarExcelAction(VacacionCabecera $vacacionCabecera)
	{

		//obtenemos los datos necesarios para exportar el formulario

		$em = $this->getDoctrine()->getManager();
		$query = $em->createQuery(
			'SELECT d
          FROM AdminVacacionBundle:VacacionDetalle d
          WHERE d.vacacionCabecera=:cabecera'
		)->setParameter('cabecera', $vacacionCabecera->getId());

		$vacacionDetalles = $query->getResult();

		if ($vacacionCabecera->getTipo() == "N") {
			// $permisos=$em->getRepository('AdminVacacionBundle:Permiso')->findByEmpleadoNoCargado($vacacionCabecera->getEmpleado()->getId());

			// $totalDiasPermiso=$this->get('cps_user_utilitario')->calcularTotalDias($permisos);
			if ($vacacionCabecera->getEstado() == 1) {
				$totalDiasPermiso = $em->getRepository('AdminVacacionBundle:Permiso')->findDiasPermisosNoCargado($vacacionCabecera->getEmpleado()->getId());
			} else {
				$totalDiasPermiso = $em->getRepository('AdminVacacionBundle:Permiso')->findDiasPermisosCargado($vacacionCabecera->getId());
			}

		} elseif ($vacacionCabecera->getTipo() == "R") {
			$totalDiasPermiso = 0;
		}

		$auxEmpleado = $em->getRepository('CpsPerPlanillaBundle:Auxempleado')->findOneByEmpleado($vacacionCabecera->getEmpleado()->getId());
		$centroCostos = $vacacionCabecera->getCentroCostos();

		$usuario = "Usuario:" . " " . $vacacionCabecera->getUsuario();


		// solicitamos el servicio 'phpexcel' y creamos el objeto vacío...
		$phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

		// ...y le asignamos una serie de propiedades
		$phpExcelObject->getProperties()
			->setCreator("Erlan")
			->setLastModifiedBy("Erlan")
			->setTitle("Formulario")
			->setSubject("solicitud vacacion")
			->setDescription("Formulario correspondiente al tramite de solicitud de vacacion");

		// establecemos como hoja activa la primera, y le asignamos un título
		$phpExcelObject->setActiveSheetIndex(0);
		$phpExcelObject->getActiveSheet()->setTitle('IMPRIMIR');

		// establecemos stylos por defecto y a celdas distintas
		$phpExcelObject->getDefaultStyle()->getFont()->setName('Arial')
			->setSize(11);
		$phpExcelObject->getActiveSheet()->getStyle('F1')->applyFromArray(array('font' => array('name' => 'Arial', 'bold' => true, 'size' => 18)));
		$phpExcelObject->getActiveSheet()->getStyle('G1')->applyFromArray(array('font' => array('name' => 'Arial', 'bold' => true, 'size' => 20)));

		// format gestion
		$gestionExcel = array();
		foreach ($vacacionDetalles as $vacacionDetalle) {
			if ($vacacionDetalle->getVacacionGestion()) {
				$arrayGestion = explode("-", $vacacionDetalle->getVacacionGestion()->getGestion());
				$arrayGestion[0] = substr($arrayGestion[0], -2);
				$arrayGestion[1] = substr($arrayGestion[1], -2);
				$gestionImp = implode("-", $arrayGestion);
				$gestionExcel[] = $gestionImp;
			}
		}

		$gImp = implode("/", $gestionExcel);

		// escribimos en distintas celdas del documento el título de los campos que vamos a exportar

		$fila = 21;
		$count = 0;
		$vacacionDetalleIm = array();
		if (count($vacacionDetalles) > 2) {
			$vacacionDetalleIm[] = $vacacionDetalles[0];
			$vacacionDetalleIm[] = end($vacacionDetalles);
		} else {
			foreach ($vacacionDetalles as $vacacionDetalle) {
				$vacacionDetalleIm[] = $vacacionDetalle;
			}
		}


		foreach ($vacacionDetalleIm as $vacacionDetalle) {
			if ($vacacionDetalle->getVacacionGestion()) {
				if ($vacacionDetalle->getVacacionGestion()->getTomados() == 0) {
					$imp = 'A CUENTA';
				} else {
					$imp = 'SALDO';
				}
				$phpExcelObject->setActiveSheetIndex(0)
					->setCellValue('C' . $fila, $vacacionDetalle->getDias())
					->setCellValue('F' . $fila, $vacacionDetalle->getVacacionGestion()->getGestion())
					->setCellValue('G' . $fila, $imp);

				$fila = $fila + 1;
			}

		}


		$phpExcelObject->setActiveSheetIndex(0)
			->setCellValue('F1', 'N°')
			->setCellValue('G1', $vacacionCabecera->getId())
			->setCellValue('B5', strtoupper($vacacionCabecera->getEmpleado()).'--'.$vacacionCabecera->getEmpleado()->getId())
			->setCellValue('A6', strtoupper($centroCostos->getSucursal()))
			->setCellValue('E6', strtoupper($centroCostos->getServicio()))
			->setCellValue('A7', $auxEmpleado ? strtoupper($auxEmpleado->getCargo()) : '')
			->setCellValue('F7', $vacacionCabecera->getFechaInicio()->format('d-m-Y'))
			->setCellValue('D10', $vacacionCabecera->getCreadoEl()->format('d'))
			->setCellValue('E10', $this->convertirEspañol($vacacionCabecera->getCreadoEl()->format('F')))
			->setCellValue('G10', $vacacionCabecera->getCreadoEl()->format('y'))
			->setCellValue('B20', $auxEmpleado ? $auxEmpleado->getIngresoEl()->format('d') . ' ' . 'DE' . ' ' .
				$this->convertirEspañol($auxEmpleado->getIngresoEl()->format('F')) . ' ' . 'DE' . ' ' .
				$auxEmpleado->getIngresoEl()->format('Y') : '')
			->setCellValue('C23', $vacacionCabecera->getTotalDias())
			->setCellValue('C26', $totalDiasPermiso)
			->setCellValue('A34', $vacacionCabecera->getFechaInicio()->format('d-m-Y'))
			->setCellValue('D34', $vacacionCabecera->getFechaFin()->format('d-m-Y'))
			->setCellValue('A35', $vacacionCabecera->getFechaRegreso()->format('d-m-Y'))
			->setCellValue('D38', $vacacionCabecera->getCreadoEl()->format('d'))
			->setCellValue('E38', $this->convertirEspañol($vacacionCabecera->getCreadoEl()->format('F')))
			->setCellValue('G38', $vacacionCabecera->getCreadoEl()->format('y'));

		if ($gImp != "") {
			$phpExcelObject->setActiveSheetIndex(0)->setCellValue('B8', $gImp)->setCellValue('D33', $gImp);
		}


		//fijamos un alto a las distintas filas
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('1')
			->setRowHeight(36);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('2')
			->setRowHeight(23.25);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('3')
			->setRowHeight(17.5);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('4')
			->setRowHeight(15);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('5')
			->setRowHeight(20);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('6')
			->setRowHeight(20);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('7')
			->setRowHeight(20);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('8')
			->setRowHeight(20);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('9')
			->setRowHeight(20);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('10')
			->setRowHeight(19.75);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('11')
			->setRowHeight(20);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('12')
			->setRowHeight(20);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('13')
			->setRowHeight(15);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('14')
			->setRowHeight(15);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('15')
			->setRowHeight(18);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('16')
			->setRowHeight(15.75);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('17')
			->setRowHeight(15);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('18')
			->setRowHeight(12);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('19')
			->setRowHeight(21);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('20')
			->setRowHeight(24);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('21')
			->setRowHeight(20);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('22')
			->setRowHeight(20);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('23')
			->setRowHeight(20);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('24')
			->setRowHeight(20);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('25')
			->setRowHeight(9.75);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('26')
			->setRowHeight(19.5);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('27')
			->setRowHeight(20);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('28')
			->setRowHeight(15);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('29')
			->setRowHeight(15);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('30')
			->setRowHeight(14);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('31')
			->setRowHeight(18);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('32')
			->setRowHeight(9.75);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('33')
			->setRowHeight(17);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('34')
			->setRowHeight(17.25);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('35')
			->setRowHeight(17.25);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('36')
			->setRowHeight(19.5);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('37')
			->setRowHeight(15);
		$phpExcelObject->setActiveSheetIndex(0)
			->getRowDimension('38')
			->setRowHeight(20.25);


		//combinamos distintas celdas
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('B5:F5');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('E10:F10');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('E38:F38');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('A35:B35');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('A34:B34');
		$phpExcelObject->setActiveSheetIndex(0)->mergeCells('D33:E33');

		//centrar campos
		$phpExcelObject->getActiveSheet()->getStyle('E10')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
		$phpExcelObject->getActiveSheet()->getStyle('E38')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
		$phpExcelObject->getActiveSheet()->getStyle('G10')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
		$phpExcelObject->getActiveSheet()->getStyle('G38')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
		$phpExcelObject->getActiveSheet()->getStyle('G1')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,));
		$phpExcelObject->getActiveSheet()->getStyle('F1')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,));
		$phpExcelObject->getActiveSheet()->getStyle('A34')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
		$phpExcelObject->getActiveSheet()->getStyle('A35')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
		$phpExcelObject->getActiveSheet()->getStyle('D33')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,));
		$phpExcelObject->getActiveSheet()->getStyle('D38')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
		$phpExcelObject->getActiveSheet()->getStyle('D10')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
		$phpExcelObject->getActiveSheet()->getStyle('D33')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));


		// fijamos un ancho a las distintas columnas

		$phpExcelObject->setActiveSheetIndex(0)
			->getColumnDimension('A')
			->setWidth(14.57);
		$phpExcelObject->setActiveSheetIndex(0)
			->getColumnDimension('B')
			->setWidth(10.71);
		$phpExcelObject->setActiveSheetIndex(0)
			->getColumnDimension('C')
			->setWidth(10.86);
		$phpExcelObject->setActiveSheetIndex(0)
			->getColumnDimension('D')
			->setWidth(13);
		$phpExcelObject->setActiveSheetIndex(0)
			->getColumnDimension('E')
			->setWidth(10.71);
		$phpExcelObject->setActiveSheetIndex(0)
			->getColumnDimension('F')
			->setWidth(10.86);
		$phpExcelObject->setActiveSheetIndex(0)
			->getColumnDimension('G')
			->setWidth(12.57);
		$phpExcelObject->setActiveSheetIndex(0)
			->getColumnDimension('H')
			->setWidth(10.71);


		$hoy = date('d-m-Y');
		// se crea el writer
		$writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
		// se crea el response
		$response = $this->get('phpexcel')->createStreamedResponse($writer);
		// y por último se añaden las cabeceras
		$dispositionHeader = $response->headers->makeDisposition(
			ResponseHeaderBag::DISPOSITION_ATTACHMENT,
			'FormVac' . $hoy .'--'. $vacacionCabecera->getId() . '.xlsx'
		);
		$response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'maxage=1');
		$response->headers->set('Content-Disposition', $dispositionHeader);

		return $response;


	}


	public function convertirEspañol($mes)
	{
		if ($mes == "January") $mes = "ENERO";
		if ($mes == "February") $mes = "FEBRERO";
		if ($mes == "March") $mes = "MARZO";
		if ($mes == "April") $mes = "ABRIL";
		if ($mes == "May") $mes = "MAYO";
		if ($mes == "June") $mes = "JUNIO";
		if ($mes == "July") $mes = "JULIO";
		if ($mes == "August") $mes = "AGOSTO";
		if ($mes == "September") $mes = "SEPTIEMBRE";
		if ($mes == "October") $mes = "OCTUBRE";
		if ($mes == "November") $mes = "NOVIEMBRE";
		if ($mes == "December") $mes = "DICIEMBRE";

		return $mes;
	}

	/**
	 * Deletes a vacacionCabecera entity.
	 *
	 * @Route("/{id}/delete/solicitud", name="vacacioncabecera_delete_admin_solicitud")
	 * @Method({"GET","POST"})
	 */
	public function deleteSolicitudAction(Request $request, VacacionCabecera $vacacionCabecera)
	{

		$em = $this->getDoctrine()->getManager();
		$vacacionDetalles = $em->getRepository('AdminVacacionBundle:VacacionDetalle')->findByVacacionCabecera($vacacionCabecera);

		foreach ($vacacionDetalles as $vacacionDetalle) {
			$em->remove($vacacionDetalle);
		}
		$em->remove($vacacionCabecera);
		$em->flush($vacacionCabecera);

		return $this->redirectToRoute('vacacioncabecera_index_admin');
	}




	public function guardarDetalle($vacacionCabecera, $vacacionGestiones)
	{
		$arrayDetalles = array();

		$diasVacacion = $vacacionCabecera->getTotalDias();
		foreach ($vacacionGestiones as $vacacionGestion) {
			$vacacionDetalle = new VacacionDetalle();
			if ($diasVacacion <= ($vacacionGestion->getDias() - $vacacionGestion->getTomados())) {
				$vacacionDetalle->setDias($diasVacacion);
				$diasVacacion = 0;
			} else {
				$vacacionDetalle->setDias($vacacionGestion->getDias() - $vacacionGestion->getTomados());
				$diasVacacion = $diasVacacion - $vacacionDetalle->getDias();
			}

			$vacacionDetalle->setVacacionCabecera($vacacionCabecera);
			$vacacionDetalle->setVacacionGestion($vacacionGestion);
			$vacacionDetalle->setEstado(1);
			$arrayDetalles[] = $vacacionDetalle;
			if ($diasVacacion < 1) {
				break;
			}
		}
		return $arrayDetalles;
	}

}
