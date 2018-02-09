<?php

namespace AdminVacacionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AdminVacacionBundle\Entity\Usuario;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use AdminVacacionBundle\Form\UserType;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller
{


    /**
     * Creates a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $userform=array('password'=>null, 'empleado'=>null);
                $form = $this->createForm('AdminVacacionBundle\Form\UserType',$userform);
                $form->handleRequest($request);
        $user = new Usuario();

        if ($form->isSubmitted() && $form->isValid()) {

            $data=$form->getData();
            $em = $this->getDoctrine()->getManager();
            $empleado=$em->getRepository('CpsPerArchivoBundle:Empleado')->findOneById($data['empleado']);
            $validUser=$em->getRepository('AdminVacacionBundle:Usuario')->findByLogin($data['empleado']);
            if(count($validUser)==0){
            $user->setLogin($data['empleado']);
            $password=$data['password'];
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $password);
            $user->setCreadoEl(new \DateTime("now"));
            $user->setPassword($encoded);
            $user->setEmpleado($empleado);
            $em->persist($user);
            $em->flush();
            $this->get('ras_flash_alert.alert_reporter')->addSuccess("Se creo exitosamente su usuario ");
            }
            else{
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'El usuario ya existe'
                );

                return $this->redirectToRoute('user_new');
            }

            return $this->redirectToRoute('login');
        }

        return $this->render('AdminVacacionBundle:user:new.html.twig', array(
            // 'user' => $user,
            'form' => $form->createView(),
        ));
    }


    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     */
    public function showAction(User $user)
    {
        $deleteForm = $this->createDeleteForm($user);

        return $this->render('user/show.html.twig', array(
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        ));
    }




}
