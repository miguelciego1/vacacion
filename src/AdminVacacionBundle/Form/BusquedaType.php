<?php

namespace AdminVacacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BusquedaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
              ->add('empleado','genemu_jqueryautocomplete_entity', array(
                                                      'class' => 'Cps\Personal\ArchivoBundle\Entity\Empleado',
                                                      'route_name' => 'buscarEmpleadoAjax',
                                                      'label'=>false,
                                                      'required'=>false)
                                                      )
             ->add('buscar','submit', array('label' => 'BUSCAR'))
             ->add('todos','submit', array('label' => 'TODOS'))
      ;
    }


}
