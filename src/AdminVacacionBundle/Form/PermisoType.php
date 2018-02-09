<?php

namespace AdminVacacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermisoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        /*->add('fechaSolicitud','date',array('required'=>true))
        ->add('fechaInicio','time',array('required'=>true))
        ->add('fechaFin','time',array('required'=>true))
        ->add('fechaRegreso','time',array('required'=>true))*/
        ->add('tiempoLicencia')
        ->add('tipo','choice',array('choices'=>array('HORAS'=>'Hora','DIAS'=>'Dia'),'label'=>'Unidad'))
        ->add('tipoPermiso')
        ->add('motivo','textarea',array('required'=>true))
        ->add('empleadoId', 'genemu_jqueryautocomplete_entity', array(
        'class' => 'Cps\Personal\ArchivoBundle\Entity\Empleado',
        'route_name' => 'buscarEmpleadoAjax',
        'mapped'=>false))
        ->add('centroCostos');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserVacacionBundle\Entity\Permiso'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'uservacacionbundle_permiso';
    }


}
