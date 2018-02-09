<?php

namespace AdminVacacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ReporteCorrelativoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
              ->add('idInicio','integer',array('required'=>true,'label'=>'Desde :'))
              ->add('idFin','integer',array('required'=>true,'label'=>'Hasta :'))
              ->add('save2', 'submit', array('label' => 'Generar PDF'))
      ;
    }

}
