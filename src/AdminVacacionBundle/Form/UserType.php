<?php

namespace AdminVacacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('empleado', 'genemu_jqueryautocomplete_entity', array(
        'class' => 'Cps\Personal\ArchivoBundle\Entity\Empleado',
        'route_name' => 'buscarEmpleadoAjax'
        ))
        ->add('password','repeated', array(
                'type' => 'password',
                'invalid_message' => 'El password no coincide',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repita Password'),
            ))

        ;
    }
}
