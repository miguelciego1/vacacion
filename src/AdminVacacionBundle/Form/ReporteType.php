<?php

namespace AdminVacacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ReporteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
              ->add('fechaInicio','date',array('required'=>true,'label'=>'Desde :'))
              ->add('fechaFin','date',array('required'=>true,'label'=>'Hasta :'))
              ->add('empleadoId', 'genemu_jqueryautocomplete_entity', array(
                  'class' => 'Cps\Personal\ArchivoBundle\Entity\Empleado',
                  'route_name' => 'buscarEmpleadoAjax',
                  'required'=>false))
              ->add('seleccion','choice',array('choices'=>array('V'=>'Vacacion','P'=>'Permiso','B'=>'Baja Medica')))
              ->add('seleccionCampo','choice',array('choices'=>array(true=>'Fecha Inicio/Fin',false=>'Fecha de Registro')))
		      ->add('formato','choice',array('choices'=>array(true=>'PDF',false=>'Excel')))
              ->add('save', 'submit', array('label' => 'GENERAR'))
      ;
    }

}
