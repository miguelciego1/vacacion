<?php

namespace AdminVacacionBundle\Controller;

use AdminVacacionBundle\Entity\VacacionGestion;
use AdminVacacionBundle\Form\VacacionGestionType;
use AdminVacacionBundle\Form\VacacionGestionDisType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Vacaciongestion controller.
 *
 * @Route("vg")
 */
class VacacionGestionController extends Controller
{

	/*========================================INDEX =============================================*/
	/*============================================================================================*/
	/**
	 * Lists all vacacionGestion entities.
	 *
	 * @Route("/", name="vacaciongestion_index")
	 * @Method({"GET","POST"})
	 */
	public function indexAction(Request $request)
	{
		$empleado = false;
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
			$vac = $em->getRepository('AdminVacacionBundle:VacacionCabecera')->findByEmpleadoDisctinct($session->get('buscarE'));
			if (!is_null($vac)) {
				$empleado = true;
			}
			$query = $em->createQuery(
				'SELECT g
            FROM AdminVacacionBundle:VacacionGestion g
            WHERE g.empleado=:empleado
            ORDER BY g.gestion DESC'
			)->setParameter('empleado', $session->get('buscarE'));
		} else {
			$empleado = false;
			$query = $em->createQuery(
				'SELECT g
            FROM AdminVacacionBundle:VacacionGestion g
            ORDER BY g.gestion DESC'
			);
		}


		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 15);
		return $this->render('AdminVacacionBundle:vacaciongestion:index.html.twig', array(
			'vacacionGestions' => $pagination,
			'formBuscar' => $form->createView(),
			'empleado' => $empleado,
		));

	}
	/*======================================================================================*/
	/*=======================================================FIN DE INDEX =====================*/


	/**
	 * Creates a new vacacionGestion entity.
	 *
	 * @Route("/new", name="vacaciongestion_new")
	 * @Method({"GET", "POST"})
	 */
	public function newAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$vacacionGestion = new Vacaciongestion();
		$form = $this->createForm(new VacacionGestionType($em), $vacacionGestion);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$vacacionGestion->setEstado(true);
			$em->persist($vacacionGestion);
			$em->flush($vacacionGestion);
			$this->get('ras_flash_alert.alert_reporter')->addSuccess("Se guardo correctamente la gestion");


			return $this->redirectToRoute('vacaciongestion_index');
		}

		return $this->render('AdminVacacionBundle:vacaciongestion:new.html.twig', array(
			'vacacionGestion' => $vacacionGestion,
			'form' => $form->createView(),
		));
	}

	/**
	 * Displays a form to edit an existing vacacionGestion entity.
	 *
	 * @Route("/{id}/edit", name="vacaciongestion_edit")
	 * @Method({"GET", "POST"})
	 */
	public function editAction(Request $request, VacacionGestion $vacacionGestion)
	{
		$em = $this->getDoctrine()->getManager();
		$editForm = $this->createForm(new VacacionGestionType($em), $vacacionGestion);
		$editForm->handleRequest($request);

		if ($editForm->isSubmitted() && $editForm->isValid()) {
			$em->flush();
			$this->get('ras_flash_alert.alert_reporter')->addSuccess("Se edito correctamente la gestion" . ' ' . $vacacionGestion->getId());
			return $this->redirectToRoute('vacaciongestion_edit', array('id' => $vacacionGestion->getId()));
		}

		return $this->render('AdminVacacionBundle:vacaciongestion:edit.html.twig', array(
			'vacacionGestion' => $vacacionGestion,
			'edit_form' => $editForm->createView(),
		));
	}

	/**
	 * Displays a form to edit an existing vacacionGestion entity.
	 *
	 * @Route("/{id}/edit/dist/", name="vacaciongestion_edit_dis")
	 * @Method({"GET", "POST"})
	 */
	public function editDisAction(Request $request, VacacionGestion $vacacionGestion)
	{
		$em = $this->getDoctrine()->getManager();
		$editForm = $this->createForm(new VacacionGestionDisType($em), $vacacionGestion);
		$editForm->handleRequest($request);

		if ($editForm->isSubmitted() && $editForm->isValid()) {
			$em->flush();
			$this->get('ras_flash_alert.alert_reporter')->addSuccess("Se edito correctamente la gestion" . ' ' . $vacacionGestion->getId());
			return $this->redirectToRoute('vacaciongestion_edit_dis', array('id' => $vacacionGestion->getId()));
		}

		return $this->render('AdminVacacionBundle:vacaciongestion:edit.html.twig', array(
			'vacacionGestion' => $vacacionGestion,
			'edit_form' => $editForm->createView(),
		));
	}

	/**
	 * Deletes a vacacionGestion entity.
	 *
	 * @Route("/{id}", name="vacaciongestion_delete")
	 * @Method({"GET", "POST"})
	 */
	public function deleteAction(VacacionGestion $vacacionGestion)
	{
		$em = $this->getDoctrine()->getManager();
		$em->remove($vacacionGestion);
		$em->flush();
		$this->get('ras_flash_alert.alert_reporter')->addSuccess("Se elimino correctamente la gestion" . ' ' . $vacacionGestion->getId());


		return $this->redirectToRoute('vacaciongestion_index');
	}

	/**
	 * Creates a form to delete a vacacionGestion entity.
	 *
	 * @param VacacionGestion $vacacionGestion The vacacionGestion entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm(VacacionGestion $vacacionGestion)
	{
		return $this->createFormBuilder()
			->setAction($this->generateUrl('vacaciongestion_delete', array('id' => $vacacionGestion->getId())))
			->setMethod('DELETE')
			->getForm();
	}


}
