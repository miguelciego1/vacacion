<?php

namespace AdminVacacionBundle\Controller;

use AdminVacacionBundle\Entity\VacacionCabecera;
use AdminVacacionBundle\Entity\VacacionConfig;
use AdminVacacionBundle\Form\VacacionConfigType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Vacacionconfig controller.
 *
 * @Route("vf")
 */
class VacacionConfigController extends Controller
{
	/**
	 * Lists all vacacionConfig entities.
	 *
	 * @Route("/", name="vacacionconfig_index")
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
				//cuando se busca un registro de un empleado cambiamos la variable de session al codigo de empleado
				$session->set('buscarE', $data['empleado']);
			} else {
				$session->set('buscarE', 0);
			}
		}

		if ($session->get('buscarE') > 0) {
			//si la variable es diferente a cero se busca registros segun la variable de session
			$query = $em->createQuery(
				'SELECT v
          FROM AdminVacacionBundle:VacacionConfig v
          WHERE v.empleado=:empleado
          ORDER BY v.id DESC'
			)->setParameter('empleado', $session->get('buscarE'));
		} else {
			$query = $em->createQuery(
				'SELECT v
          FROM AdminVacacionBundle:VacacionConfig v
          ORDER BY v.id DESC
          ');
		}


		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 15);

		return $this->render('AdminVacacionBundle:vacacionconfig:index.html.twig', array(
			'vacacionConfigs' => $pagination,
			'formBuscar' => $form->createView(),
		));
	}

	/**
	 * Creates a new vacacionConfig entity.
	 *
	 * @Route("/new", name="vacacionconfig_new")
	 * @Method({"GET", "POST"})
	 */
	public function newAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$vacacionConfig = new Vacacionconfig();
		$form = $this->createForm(new VacacionConfigType($em), $vacacionConfig);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$gestion = $em->getRepository('AdminVacacionBundle:VacacionGestion')->findOneBy(
				array('gestion' => $vacacionConfig->getGestion(), 'empleado' => $vacacionConfig->getEmpleado()->getId()));
			$permiso = $em->getRepository('AdminVacacionBundle:Permiso')->findOneBy(
				array('id' => $vacacionConfig->getPermiso(), 'empleado' => $vacacionConfig->getEmpleado()->getId()));

			if (is_null($gestion)) {
				$this->get('ras_flash_alert.alert_reporter')->addError("No se encontraron gestion del empleado especificado");
				return $this->render('AdminVacacionBundle:vacacionconfig:new.html.twig', array(
					'vacacionConfig' => $vacacionConfig,
					'form' => $form->createView(),
				));
			} elseif ($vacacionConfig->getDias() > $gestion->getDias()) {
				$this->get('ras_flash_alert.alert_reporter')->addError("Los dias sobrepasan los dias que le corresponde");
				return $this->render('AdminVacacionBundle:vacacionconfig:new.html.twig', array(
					'vacacionConfig' => $vacacionConfig,
					'form' => $form->createView(),
				));
			}

			$em->persist($vacacionConfig);
			$em->flush();

			$this->get('ras_flash_alert.alert_reporter')->addSuccess("Se registro correctamente ");
            return $this->redirectToRoute('vacacionconfig_edit', array('id' => $vacacionConfig->getId()));
		}

		return $this->render('AdminVacacionBundle:vacacionconfig:new.html.twig', array(
			'vacacionConfig' => $vacacionConfig,
			'form' => $form->createView(),
		));
	}

	/**
	 * Finds and displays a vacacionConfig entity.
	 *
	 * @Route("/{id}", name="vacacionconfig_show")
	 * @Method("GET")
	 */
	public function showAction(VacacionConfig $vacacionConfig)
	{
		$deleteForm = $this->createDeleteForm($vacacionConfig);

		return $this->render('vacacionconfig/show.html.twig', array(
			'vacacionConfig' => $vacacionConfig,
			'delete_form' => $deleteForm->createView(),
		));
	}

	/**
	 * Displays a form to edit an existing vacacionConfig entity.
	 *
	 * @Route("/{id}/edit", name="vacacionconfig_edit")
	 * @Method({"GET", "POST"})
	 */
	public function editAction(Request $request, VacacionConfig $vacacionConfig)
	{
		$em = $this->getDoctrine()->getManager();
		$deleteForm = $this->createDeleteForm($vacacionConfig);
		$editForm = $this->createForm(new VacacionConfigType($em), $vacacionConfig);
		$editForm->handleRequest($request);

		if ($editForm->isSubmitted() && $editForm->isValid()) {
			$gestion = $em->getRepository('AdminVacacionBundle:VacacionGestion')->findOneBy(
				array('gestion' => $vacacionConfig->getGestion(), 'empleado' => $vacacionConfig->getEmpleado()->getId()));
			$permiso = $em->getRepository('AdminVacacionBundle:Permiso')->findOneBy(
				array('id' => $vacacionConfig->getPermiso(), 'empleado' => $vacacionConfig->getEmpleado()->getId()));

			if (is_null($gestion)) {
				$this->get('ras_flash_alert.alert_reporter')->addError("No se encontraron gestion del empleado especificado");
				return $this->redirectToRoute('vacacionconfig_edit', array('id' => $vacacionConfig->getId()));
			}
			$em->flush();
			$this->get('ras_flash_alert.alert_reporter')->addSuccess("Se edito correctamente ");
			return $this->redirectToRoute('vacacionconfig_edit', array('id' => $vacacionConfig->getId()));
		}

		return $this->render('AdminVacacionBundle:vacacionconfig:edit.html.twig', array(
			'vacacionConfig' => $vacacionConfig,
			'edit_form' => $editForm->createView(),
			'delete_form' => $deleteForm->createView(),
		));
	}

	/**
	 * Deletes a vacacionConfig entity.
	 *
	 * @Route("/{id}", name="vacacionconfig_delete")
	 * @Method("DELETE")
	 */
	public function deleteAction(Request $request, VacacionConfig $vacacionConfig)
	{
		$form = $this->createDeleteForm($vacacionConfig);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($vacacionConfig);
			$em->flush();
		}

		return $this->redirectToRoute('vacacionconfig_index');
	}

	/**
	 * Creates a form to delete a vacacionConfig entity.
	 *
	 * @param VacacionConfig $vacacionConfig The vacacionConfig entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm(VacacionConfig $vacacionConfig)
	{
		return $this->createFormBuilder()
			->setAction($this->generateUrl('vacacionconfig_delete', array('id' => $vacacionConfig->getId())))
			->setMethod('DELETE')
			->getForm();
	}

	/**
	 * actualizar a vacacionConfig entity.
	 *
	 * @Route("/actualizar/{id}", name="vacacionconfig_actuaalizar")
	 * @Method({"GET", "POST"})
	 */
	public function actualizar(Request $request, VacacionConfig $vacacionConfig)
	{
		$em = $this->getDoctrine()->getManager();
		$em->getRepository('AdminVacacionBundle:Permiso')->actualizarMayorId($vacacionConfig->getPermiso(), $vacacionConfig->getEmpleado()->getId());
		$em->getRepository('AdminVacacionBundle:Permiso')->actualizarMenorId($vacacionConfig->getPermiso(), $vacacionConfig->getEmpleado()->getId());

		$gestionesLimpias = $em->getRepository('AdminVacacionBundle:VacacionGestion')->findByGestionUtilizadas($vacacionConfig->getGestion(), $vacacionConfig->getEmpleado()->getId());

		foreach ($gestionesLimpias as $gestion) {
			if ($gestion->getGestion() == $vacacionConfig->getGestion()) {
				$gestion->setTomados($vacacionConfig->getDias());
			} else {
				$gestion->setTomados(0);
			}

		}
		$em->flush();
		$vacacionCabeceracontroller = new VacacionCabeceraController();
		$vacaciones = $em->getRepository('AdminVacacionBundle:VacacionCabecera')->findByEmpleadoconfirmados($vacacionConfig->getEmpleado()->getId());

		foreach ($vacaciones as $vacacion) {
			if (!$vacacion->getAnulado()) {
				$gestiones = $em->getRepository('AdminVacacionBundle:VacacionGestion')->findVacacionGestionDisponibleByEmpleado($vacacionConfig->getEmpleado()->getId(), 'N');
				$em->getRepository('AdminVacacionBundle:VacacionDetalle')->deleteByVacacion($vacacion->getId());
				$detalles = $vacacionCabeceracontroller->guardarDetalle($vacacion, $gestiones);
				foreach ($detalles as $detalle) {
					if ($vacacion->getEstado() == 1) {
						$detalle->setEstado(1);
					} else {
						$detalle->setEstado(2);
						$detalle->getVacacionGestion()->setTomados($detalle->getDias() + $detalle->getVacacionGestion()->getTomados());

					}
					$em->persist($detalle);
				}
				if ($vacacion->getEstado() != 1) {
					$vacacion->setEstado(2);
				}

				$em->flush();
			}
			if ($vacacion->getEstado()!=1)
			{

				$gestionesP = $em->getRepository('AdminVacacionBundle:VacacionGestion')->findVacacionGestionDisponibleByEmpleado($vacacionConfig->getEmpleado()->getId(), 'N');
				$vacacionNew = new VacacionCabecera();
				$vacacionNew->setTotalDias($em->getRepository('AdminVacacionBundle:Permiso')->findDiasPermisosCargado($vacacion->getId()));

				$detallesP = $vacacionCabeceracontroller->guardarDetalle($vacacionNew, $gestionesP);
				foreach ($detallesP as $detalleP) {
					$gestion = $detalleP->getVacacionGestion();
					$gestion->setTomados($detalleP->getDias() + $gestion->getTomados());
				}
				$em->flush();
			}
		}

		$this->get('ras_flash_alert.alert_reporter')->addSuccess("Se actualizo correctamente ");
		return $this->redirectToRoute('vacacionconfig_index');
	}
}
