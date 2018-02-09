<?php

namespace UserVacacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class PermisoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('fechaInicio', 'datetime' ,array('required'=>true))
        ->add('tiempoLicencia')
        ->add('tipo','choice',array('choices'=>array('HORAS'=>'Hora','DIAS'=>'Dia'),'label'=>'Unidad'))
        ->add('tipoPermiso')
        ->add('centroCostos')
        ->add('motivo','textarea',array('required'=>true));
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
